<?php
namespace App\Services;

use App\Models\Setting;

class Mailer
{
	private string $host;
	private int $port;
	private string $username;
	private string $password;
	private string $encryption; // tls|ssl|none
	private string $fromEmail;
	private string $fromName;

	public function __construct()
	{
		$this->host = Setting::get('smtp.host', '');
		$this->port = (int)Setting::get('smtp.port', '587');
		$this->username = Setting::get('smtp.username', '');
		$this->password = Setting::get('smtp.password', '');
		$this->encryption = Setting::get('smtp.encryption', 'tls');
		$this->fromEmail = Setting::get('smtp.from_email', 'no-reply@example.com');
		$this->fromName = Setting::get('smtp.from_name', 'Ticko');
	}

	public function send(string $toEmail, string $subject, string $htmlBody): bool
	{
		if ($this->host === '' || $this->username === '' || $this->password === '') {
			// Fallback: simple mail()
			$headers = "MIME-Version: 1.0\r\n" .
				"Content-type:text/html;charset=UTF-8\r\n" .
				"From: {$this->fromName} <{$this->fromEmail}>\r\n";
			return @mail($toEmail, $subject, $htmlBody, $headers);
		}

		$transport = ($this->encryption === 'ssl') ? 'ssl://' : '';
		$socket = fsockopen($transport . $this->host, ($this->encryption === 'ssl') ? 465 : $this->port, $errno, $errstr, 10);
		if (!$socket) { return false; }

		$read = function() use ($socket) { return fgets($socket, 512); };
		$write = function($cmd) use ($socket) { fwrite($socket, $cmd . "\r\n"); };

		$read();
		$write('EHLO localhost'); $read();
		if ($this->encryption === 'tls') { $write('STARTTLS'); $read(); stream_socket_enable_crypto($socket, true, STREAM_CRYPTO_METHOD_TLS_CLIENT); $write('EHLO localhost'); $read(); }
		$write('AUTH LOGIN'); $read();
		$write(base64_encode($this->username)); $read();
		$write(base64_encode($this->password)); $read();
		$write('MAIL FROM: <' . $this->fromEmail . '>'); $read();
		$write('RCPT TO: <' . $toEmail . '>'); $read();
		$write('DATA'); $read();
		$headers = "From: {$this->fromName} <{$this->fromEmail}>\r\n" . "MIME-Version: 1.0\r\n" . "Content-Type: text/html; charset=UTF-8\r\n";
		$message = $headers . "Subject: {$subject}\r\n\r\n" . $htmlBody . "\r\n.";
		$write($message); $read();
		$write('QUIT');
		fclose($socket);
		return true;
	}
}


