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
		$this->fromName = Setting::get('smtp.from_name', 'ShikaTicket');
	}

	public function send(string $toEmail, string $subject, string $htmlBody): bool
	{
		// For local development (ngrok, localhost), log email instead of sending
		$isLocalDev = strpos($_SERVER['HTTP_HOST'] ?? '', 'localhost') !== false || 
					  strpos($_SERVER['HTTP_HOST'] ?? '', '127.0.0.1') !== false ||
					  strpos($_SERVER['HTTP_HOST'] ?? '', 'ngrok') !== false;
		
		if ($isLocalDev) {
			// Log the email instead of sending (for local development)
			$logMessage = "LOCAL DEV EMAIL:\n" .
						  "To: $toEmail\n" .
						  "Subject: $subject\n" .
						  "From: {$this->fromName} <{$this->fromEmail}>\n" .
						  "Body: $htmlBody\n" .
						  "Time: " . date('Y-m-d H:i:s') . "\n" .
						  "---\n";
			
			error_log($logMessage);
			
			// Also save to a file for easy viewing
			file_put_contents('email_log.txt', $logMessage, FILE_APPEND | LOCK_EX);
			
			return true; // Return true to indicate "success" for testing
		}
		
		if ($this->host === '' || $this->username === '' || $this->password === '') {
			// Fallback: simple mail()
			$headers = "MIME-Version: 1.0\r\n" .
				"Content-type:text/html;charset=UTF-8\r\n" .
				"From: {$this->fromName} <{$this->fromEmail}>\r\n";
			return @mail($toEmail, $subject, $htmlBody, $headers);
		}

		try {
			// Handle SSL connection
			if ($this->encryption === 'ssl') {
				$context = stream_context_create([
					'ssl' => [
						'verify_peer' => false,
						'verify_peer_name' => false,
						'allow_self_signed' => true
					]
				]);
				$socket = stream_socket_client(
					"ssl://{$this->host}:{$this->port}", 
					$errno, 
					$errstr, 
					10, 
					STREAM_CLIENT_CONNECT, 
					$context
				);
			} else {
				// Handle non-SSL connection
				$socket = fsockopen($this->host, $this->port, $errno, $errstr, 10);
			}
			
			if (!$socket) {
				error_log("SMTP connection failed: $errstr ($errno)");
				return false;
			}

			$read = function() use ($socket) { 
				$line = fgets($socket, 512); 
				error_log("SMTP <- " . trim($line));
				return $line; 
			};
			$write = function($cmd) use ($socket) { 
				error_log("SMTP -> $cmd");
				fwrite($socket, $cmd . "\r\n"); 
			};

			// Read initial greeting
			$read();
			
			// EHLO
			$write('EHLO localhost');
			$read();
			
			// Handle TLS encryption
			if ($this->encryption === 'tls') { 
				$write('STARTTLS'); 
				$starttlsResponse = $read();
				
				if (strpos($starttlsResponse, '220') === 0) {
					// Try different TLS methods
					$tlsMethods = [
						STREAM_CRYPTO_METHOD_TLS_CLIENT,
						STREAM_CRYPTO_METHOD_TLSv1_2_CLIENT,
						STREAM_CRYPTO_METHOD_TLSv1_1_CLIENT,
						STREAM_CRYPTO_METHOD_TLSv1_0_CLIENT
					];
					
					$tlsSuccess = false;
					foreach ($tlsMethods as $method) {
						if (@stream_socket_enable_crypto($socket, true, $method)) {
							$tlsSuccess = true;
							error_log("TLS encryption enabled with method: $method");
							break;
						}
					}
					
					if (!$tlsSuccess) {
						error_log('All TLS encryption methods failed');
						fclose($socket);
						return false;
					}
					
					$write('EHLO localhost'); 
					$read(); 
				} else {
					error_log("STARTTLS not supported: $starttlsResponse");
					fclose($socket);
					return false;
				}
			}
			
			// Authentication
			$write('AUTH LOGIN'); 
			$authResponse = $read();
			if (strpos($authResponse, '334') !== 0) {
				error_log("AUTH LOGIN not supported: $authResponse");
				fclose($socket);
				return false;
			}
			
			$write(base64_encode($this->username)); 
			$read();
			$write(base64_encode($this->password)); 
			$passwordResponse = $read();
			
			if (strpos($passwordResponse, '235') !== 0) {
				error_log("Authentication failed: $passwordResponse");
				fclose($socket);
				return false;
			}
			
			// Send email
			$write('MAIL FROM: <' . $this->fromEmail . '>'); 
			$read();
			$write('RCPT TO: <' . $toEmail . '>'); 
			$read();
			$write('DATA'); 
			$read();
			
			$headers = "From: {$this->fromName} <{$this->fromEmail}>\r\n" . 
					   "MIME-Version: 1.0\r\n" . 
					   "Content-Type: text/html; charset=UTF-8\r\n";
			$message = $headers . "Subject: {$subject}\r\n\r\n" . $htmlBody . "\r\n.";
			$write($message); 
			$read();
			$write('QUIT');
			fclose($socket);
			return true;
			
		} catch (\Exception $e) {
			error_log('SMTP Error: ' . $e->getMessage());
			if (isset($socket)) {
				fclose($socket);
			}
			return false;
		}
	}
}


