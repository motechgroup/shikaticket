<?php /** @var array $booking */ /** @var array $payments */ /** @var array $tickets */ ?>
<div class="max-w-4xl mx-auto px-4 py-8">
    <div class="mb-6">
        <a href="<?php echo base_url('/user/travel-bookings'); ?>" class="link">← Back to Travel Bookings</a>
    </div>

    <div class="flex items-center gap-3 mb-6">
        <h1 class="text-2xl font-bold">Travel Booking Details</h1>
        <span class="badge <?php echo $booking['status'] === 'confirmed' ? 'bg-green-600' : ($booking['status'] === 'pending' ? 'bg-yellow-600' : 'bg-red-600'); ?>">
            <?php echo ucfirst($booking['status']); ?>
        </span>
    </div>

    <?php if (!empty($booking['booking_reference'])): ?>
    <div class="card p-4 mb-6 bg-blue-900/20 border border-blue-600/30">
        <div class="flex items-center justify-between">
            <div>
                <span class="text-blue-300 font-medium">Booking Reference</span>
                <div class="text-2xl font-bold text-blue-100"><?php echo htmlspecialchars($booking['booking_reference']); ?></div>
            </div>
            <button onclick="copyToClipboard('<?php echo $booking['booking_reference']; ?>')" class="btn btn-secondary btn-sm">
                📋 Copy Reference
            </button>
        </div>
    </div>
    <?php endif; ?>

    <div class="grid lg:grid-cols-2 gap-8">
        <!-- Booking Information -->
        <div class="space-y-6">
            <div class="card p-6">
                <h2 class="text-xl font-semibold mb-4">Booking Information</h2>
                
                <div class="space-y-4">
                    <div>
                        <span class="text-gray-400 block">Booking ID</span>
                        <span class="font-medium">#<?php echo $booking['id']; ?></span>
                    </div>
                    
                    <?php if (!empty($booking['booking_reference'])): ?>
                    <div>
                        <span class="text-gray-400 block">Booking Reference</span>
                        <span class="font-medium text-lg"><?php echo htmlspecialchars($booking['booking_reference']); ?></span>
                        <button onclick="copyToClipboard('<?php echo $booking['booking_reference']; ?>')" class="btn btn-secondary btn-sm mt-1">
                            Copy Reference
                        </button>
                    </div>
                    <?php endif; ?>
                    
                    <div>
                        <span class="text-gray-400 block">Destination</span>
                        <span class="font-medium"><?php echo htmlspecialchars($booking['destination_title']); ?></span>
                        <div class="text-sm text-gray-400"><?php echo htmlspecialchars($booking['destination']); ?></div>
                    </div>
                    
                    <div>
                        <span class="text-gray-400 block">Travel Agency</span>
                        <span class="font-medium"><?php echo htmlspecialchars($booking['company_name']); ?></span>
                        <div class="text-sm text-gray-400">
                            Contact: <?php echo htmlspecialchars($booking['contact_person']); ?><br>
                            Email: <?php echo htmlspecialchars($booking['agency_email']); ?><br>
                            Phone: <?php echo htmlspecialchars($booking['agency_phone']); ?>
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <span class="text-gray-400 block">Departure Date</span>
                            <span class="font-medium"><?php echo date('M j, Y', strtotime($booking['departure_date'])); ?></span>
                        </div>
                        <div>
                            <span class="text-gray-400 block">Departure Location</span>
                            <span class="font-medium"><?php echo htmlspecialchars($booking['departure_location'] ?? 'TBA'); ?></span>
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <span class="text-gray-400 block">Participants</span>
                            <span class="font-medium"><?php echo (int)$booking['participants_count']; ?> person(s)</span>
                        </div>
                        <div>
                            <span class="text-gray-400 block">Booking Date</span>
                            <span class="font-medium"><?php echo date('M j, Y H:i', strtotime($booking['booking_date'])); ?></span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Payment Information -->
            <?php if (!empty($payments)): ?>
            <div class="card p-6">
                <h2 class="text-xl font-semibold mb-4">Payment Information</h2>
                
                <div class="space-y-4">
                    <?php foreach ($payments as $payment): ?>
                    <div class="border border-gray-700 rounded-lg p-4">
                        <div class="flex justify-between items-start mb-2">
                            <div>
                                <span class="font-medium"><?php echo ucfirst($payment['payment_method']); ?> Payment</span>
                                <span class="badge <?php echo $payment['payment_status'] === 'successful' ? 'bg-green-600' : ($payment['payment_status'] === 'pending' ? 'bg-yellow-600' : 'bg-red-600'); ?> ml-2">
                                    <?php echo ucfirst($payment['payment_status']); ?>
                                </span>
                            </div>
                            <span class="text-lg font-semibold"><?php echo htmlspecialchars($payment['currency']); ?> <?php echo number_format((float)$payment['amount'], 2); ?></span>
                        </div>
                        
                        <div class="text-sm text-gray-400 space-y-1">
                            <div>Payment Date: <?php echo date('M j, Y H:i', strtotime($payment['created_at'])); ?></div>
                            <?php if (!empty($payment['transaction_reference'])): ?>
                            <div>Reference: <?php echo htmlspecialchars($payment['transaction_reference']); ?></div>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>
        </div>

        <!-- Tickets -->
        <div class="space-y-6">
            <?php if (!empty($tickets)): ?>
            <div class="card p-6">
                <h2 class="text-xl font-semibold mb-4">Travel Tickets</h2>
                
                <div class="space-y-6">
                    <?php foreach ($tickets as $ticket): ?>
                    <div class="border border-gray-700 rounded-lg p-6">
                        <div class="flex justify-between items-start mb-4">
                            <div>
                                <span class="font-medium">Ticket Code</span>
                                <span class="badge bg-blue-600 ml-2 text-lg"><?php echo htmlspecialchars($ticket['ticket_code']); ?></span>
                            </div>
                            <span class="badge <?php echo $ticket['status'] === 'valid' ? 'bg-green-600' : 'bg-gray-600'; ?>">
                                <?php echo ucfirst($ticket['status']); ?>
                            </span>
                        </div>
                        
                        <!-- QR Code Display -->
                        <div class="flex items-center justify-center mb-4">
                            <div class="bg-white p-4 rounded-lg">
                                <?php
                                // Inline data URI to avoid CSP and external failures
                                $qrInline = '';
                                try {
                                    $rel = $ticket['qr_path'] ?? '';
                                    $abs = $rel !== '' ? (__DIR__ . '/../../' . ltrim($rel, '/')) : '';
                                    $png = '';
                                    if ($abs !== '' && is_file($abs)) {
                                        $png = @file_get_contents($abs);
                                    }
                                    if ($png === '' || $png === false) {
                                        $external = 'https://api.qrserver.com/v1/create-qr-code/?size=300x300&data=' . urlencode($ticket['ticket_code']);
                                        $png = @file_get_contents($external);
                                        // Best-effort persist for next time
                                        if ($png !== false) {
                                            $dir = __DIR__ . '/../../uploads/qrs/travel/';
                                            if (!is_dir($dir)) { @mkdir($dir, 0775, true); }
                                            @file_put_contents($dir . $ticket['ticket_code'] . '.png', $png);
                                        }
                                    }
                                    if ($png !== false && $png !== '') {
                                        $qrInline = 'data:image/png;base64,' . base64_encode($png);
                                    }
                                } catch (\Throwable $e) {}
                                $finalSrc = $qrInline !== '' ? $qrInline : base_url('/travel-tickets/qr?code=' . urlencode($ticket['ticket_code']) . '&v=' . time());
                                ?>
                                <img src="<?php echo $finalSrc; ?>" alt="QR Code for <?php echo htmlspecialchars($ticket['ticket_code']); ?>" class="w-48 h-48 object-contain" loading="lazy">
                            </div>
                        </div>
                        
                        <div class="text-sm text-gray-400 mb-4">
                            Issued: <?php echo date('M j, Y H:i', strtotime($ticket['created_at'])); ?>
                        </div>
                        
                        <!-- Action Buttons -->
                        <div class="flex gap-3 flex-wrap">
                            <button onclick="copyToClipboard('<?php echo $ticket['ticket_code']; ?>')" class="btn btn-secondary btn-sm">
                                📋 Copy Ticket Code
                            </button>
                            <button onclick="downloadQRCode('<?php echo $ticket['ticket_code']; ?>')" class="btn btn-secondary btn-sm">
                                📱 Download QR Code
                            </button>
                            <a href="<?php echo base_url('/travel-tickets/download?code=' . urlencode($ticket['ticket_code'])); ?>" class="btn btn-primary btn-sm">
                                📄 Download PDF Ticket
                            </a>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>

            <!-- Quick Actions -->
            <div class="card p-6">
                <h2 class="text-xl font-semibold mb-4">Quick Actions</h2>
                
                <div class="space-y-3">
                    <a href="<?php echo base_url('/travel/destination?id=' . $booking['destination_id']); ?>" class="btn btn-secondary w-full">
                        View Destination
                    </a>
                    
                    <?php if ($booking['status'] === 'confirmed'): ?>
                    <?php if (!empty($tickets)): ?>
                    <button onclick="downloadTicket()" class="btn btn-secondary w-full">
                        📄 Download Text Ticket
                    </button>
                    <?php else: ?>
                    <!-- Fallback download option when no tickets exist -->
                    <button onclick="downloadBookingPDF('<?php echo $booking['booking_reference'] ?? $booking['id']; ?>')" class="btn btn-primary w-full">
                        📄 Download Booking PDF
                    </button>
                    <?php endif; ?>
                    <?php endif; ?>
                    
                    <a href="<?php echo base_url('/user/travel-bookings'); ?>" class="btn btn-secondary w-full">
                        Back to Travel Bookings
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Payment success popup
function showPaymentSuccessPopup() {
    const popup = document.createElement('div');
    popup.className = 'fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50';
    popup.innerHTML = `
        <div class="bg-white rounded-lg p-8 max-w-md mx-4 text-center">
            <div class="text-green-500 text-6xl mb-4">✓</div>
            <h2 class="text-2xl font-bold text-gray-800 mb-4">Payment Successful!</h2>
            <p class="text-gray-600 mb-6">Your travel booking has been confirmed. You will receive an email confirmation shortly.</p>
            <button onclick="closePaymentSuccessPopup()" class="bg-green-500 hover:bg-green-600 text-white px-6 py-2 rounded-lg font-medium">
                Continue
            </button>
        </div>
    `;
    
    document.body.appendChild(popup);
    
    // Auto close after 5 seconds
    setTimeout(() => {
        closePaymentSuccessPopup();
    }, 5000);
}

function closePaymentSuccessPopup() {
    const popup = document.querySelector('.fixed.inset-0.bg-black.bg-opacity-50');
    if (popup) {
        popup.remove();
    }
}

// Check if we should show success popup (from URL parameter or flash message)
document.addEventListener('DOMContentLoaded', function() {
    const urlParams = new URLSearchParams(window.location.search);
    const paymentSuccess = urlParams.get('payment_success');
    
    if (paymentSuccess === '1') {
        showPaymentSuccessPopup();
        // Clean up URL
        const newUrl = window.location.pathname + window.location.search.replace(/[?&]payment_success=1/, '');
        window.history.replaceState({}, document.title, newUrl);
    } else {
        // Payment confirmation is now handled on the dashboard page
        // No floating button needed here
    }
});


function copyToClipboard(text) {
    navigator.clipboard.writeText(text).then(function() {
        alert('Copied to clipboard!');
    }, function(err) {
        console.error('Could not copy text: ', err);
    });
}

function downloadQRCode(ticketCode) {
    const qrUrl = `https://api.qrserver.com/v1/create-qr-code/?size=300x300&data=${encodeURIComponent(ticketCode)}`;
    
    fetch(qrUrl)
        .then(response => response.blob())
        .then(blob => {
            const url = window.URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = `qr-code-${ticketCode}.png`;
            document.body.appendChild(a);
            a.click();
            document.body.removeChild(a);
            window.URL.revokeObjectURL(url);
        })
        .catch(error => {
            console.error('Error downloading QR code:', error);
            alert('Failed to download QR code. Please try again.');
        });
}

function downloadTicketPDF(ticketCode, bookingRef) {
    // Create a comprehensive ticket PDF content
    const ticketData = {
        ticketCode: ticketCode,
        bookingReference: bookingRef || '<?php echo $booking['id']; ?>',
        destination: '<?php echo htmlspecialchars($booking['destination_title']); ?>',
        destinationDetails: '<?php echo htmlspecialchars($booking['destination']); ?>',
        agency: '<?php echo htmlspecialchars($booking['company_name']); ?>',
        contactPerson: '<?php echo htmlspecialchars($booking['contact_person']); ?>',
        agencyEmail: '<?php echo htmlspecialchars($booking['agency_email']); ?>',
        agencyPhone: '<?php echo htmlspecialchars($booking['agency_phone']); ?>',
        departureDate: '<?php echo date('M j, Y', strtotime($booking['departure_date'])); ?>',
        departureLocation: '<?php echo htmlspecialchars($booking['departure_location'] ?? 'TBA'); ?>',
        participants: '<?php echo (int)$booking['participants_count']; ?>',
        totalAmount: '<?php echo htmlspecialchars($booking['currency']); ?> <?php echo number_format((float)$booking['total_amount'], 2); ?>',
        bookingDate: '<?php echo date('M j, Y H:i', strtotime($booking['booking_date'])); ?>',
        qrCodeUrl: `https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=${encodeURIComponent(ticketCode)}`
    };
    
    // Create HTML content for the ticket
    const htmlContent = `
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset="UTF-8">
        <title>Travel Ticket - ${ticketData.ticketCode}</title>
        <style>
            body { font-family: Arial, sans-serif; margin: 20px; background: white; }
            .ticket { border: 2px solid #333; padding: 20px; max-width: 600px; margin: 0 auto; }
            .header { text-align: center; border-bottom: 2px solid #333; padding-bottom: 15px; margin-bottom: 20px; }
            .ticket-code { font-size: 24px; font-weight: bold; color: #2563eb; margin: 10px 0; }
            .section { margin: 15px 0; }
            .section h3 { color: #333; border-bottom: 1px solid #ccc; padding-bottom: 5px; }
            .info-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin: 10px 0; }
            .info-item { margin: 5px 0; }
            .label { font-weight: bold; color: #666; }
            .value { color: #333; }
            .qr-section { text-align: center; margin: 20px 0; }
            .qr-code { border: 1px solid #ccc; padding: 10px; display: inline-block; }
            .footer { text-align: center; margin-top: 30px; padding-top: 15px; border-top: 1px solid #ccc; color: #666; }
            @media print { body { margin: 0; } .ticket { border: none; box-shadow: none; } }
        </style>
    </head>
    <body>
        <div class="ticket">
            <div class="header">
                <h1>ShikaTicket</h1>
                <h2>TRAVEL TICKET</h2>
                <div class="ticket-code">${ticketData.ticketCode}</div>
            </div>
            
            <div class="section">
                <h3>Booking Information</h3>
                <div class="info-grid">
                    <div class="info-item">
                        <span class="label">Booking Reference:</span><br>
                        <span class="value">${ticketData.bookingReference}</span>
                    </div>
                    <div class="info-item">
                        <span class="label">Booking Date:</span><br>
                        <span class="value">${ticketData.bookingDate}</span>
                    </div>
                </div>
            </div>
            
            <div class="section">
                <h3>Travel Details</h3>
                <div class="info-grid">
                    <div class="info-item">
                        <span class="label">Destination:</span><br>
                        <span class="value">${ticketData.destination}</span>
                        <br><small style="color: #666;">${ticketData.destinationDetails}</small>
                    </div>
                    <div class="info-item">
                        <span class="label">Departure Date:</span><br>
                        <span class="value">${ticketData.departureDate}</span>
                    </div>
                    <div class="info-item">
                        <span class="label">Departure Location:</span><br>
                        <span class="value">${ticketData.departureLocation}</span>
                    </div>
                    <div class="info-item">
                        <span class="label">Participants:</span><br>
                        <span class="value">${ticketData.participants} person(s)</span>
                    </div>
                </div>
            </div>
            
            <div class="section">
                <h3>Travel Agency</h3>
                <div class="info-grid">
                    <div class="info-item">
                        <span class="label">Agency:</span><br>
                        <span class="value">${ticketData.agency}</span>
                    </div>
                    <div class="info-item">
                        <span class="label">Contact Person:</span><br>
                        <span class="value">${ticketData.contactPerson}</span>
                    </div>
                    <div class="info-item">
                        <span class="label">Email:</span><br>
                        <span class="value">${ticketData.agencyEmail}</span>
                    </div>
                    <div class="info-item">
                        <span class="label">Phone:</span><br>
                        <span class="value">${ticketData.agencyPhone}</span>
                    </div>
                </div>
            </div>
            
            <div class="section">
                <h3>Payment Information</h3>
                <div class="info-item">
                    <span class="label">Total Amount:</span>
                    <span class="value">${ticketData.totalAmount}</span>
                </div>
            </div>
            
            <div class="qr-section">
                <h3>QR Code for Scanning</h3>
                <div class="qr-code">
                    <img src="${ticketData.qrCodeUrl}" alt="QR Code" style="width: 200px; height: 200px;">
                </div>
                <p style="margin-top: 10px; color: #666;">Present this QR code at departure</p>
            </div>
            
            <div class="footer">
                <p><strong>Important:</strong> Please arrive at least 30 minutes before departure time.</p>
                <p>Keep this ticket safe and present it at departure. You can also show the QR code on your mobile device.</p>
                <p style="margin-top: 15px; color: #999;">Generated on ${new Date().toLocaleString()}</p>
            </div>
        </div>
    </body>
    </html>
    `;
    
    // Create a new window with the HTML content and trigger print
    const printWindow = window.open('', '_blank');
    printWindow.document.write(htmlContent);
    printWindow.document.close();
    
    // Wait for the page to load, then trigger print
    printWindow.onload = function() {
        setTimeout(() => {
            printWindow.print();
            printWindow.close();
        }, 500);
    };
}

function downloadTicket() {
    // Create a simple text ticket
    const ticketContent = `
TRAVEL TICKET
=============
Destination: <?php echo htmlspecialchars($booking['destination_title']); ?>
Agency: <?php echo htmlspecialchars($booking['company_name']); ?>
Departure: <?php echo date('M j, Y', strtotime($booking['departure_date'])); ?>
Ticket Code: <?php echo !empty($tickets) ? $tickets[0]['ticket_code'] : 'N/A'; ?>
Participants: <?php echo (int)$booking['participants_count']; ?>

Please present this ticket code at departure.
    `;
    
    const blob = new Blob([ticketContent], { type: 'text/plain' });
    const url = window.URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = 'travel-ticket-<?php echo $booking['id']; ?>.txt';
    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);
    window.URL.revokeObjectURL(url);
}

function downloadBookingPDF(bookingRef) {
    // Create a booking PDF using booking reference as ticket code
    const bookingData = {
        ticketCode: bookingRef,
        bookingReference: bookingRef,
        destination: '<?php echo htmlspecialchars($booking['destination_title']); ?>',
        destinationDetails: '<?php echo htmlspecialchars($booking['destination']); ?>',
        agency: '<?php echo htmlspecialchars($booking['company_name']); ?>',
        contactPerson: '<?php echo htmlspecialchars($booking['contact_person']); ?>',
        agencyEmail: '<?php echo htmlspecialchars($booking['agency_email']); ?>',
        agencyPhone: '<?php echo htmlspecialchars($booking['agency_phone']); ?>',
        departureDate: '<?php echo date('M j, Y', strtotime($booking['departure_date'])); ?>',
        departureLocation: '<?php echo htmlspecialchars($booking['departure_location'] ?? 'TBA'); ?>',
        participants: '<?php echo (int)$booking['participants_count']; ?>',
        totalAmount: '<?php echo htmlspecialchars($booking['currency']); ?> <?php echo number_format((float)$booking['total_amount'], 2); ?>',
        bookingDate: '<?php echo date('M j, Y H:i', strtotime($booking['booking_date'])); ?>',
        qrCodeUrl: `https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=${encodeURIComponent(bookingRef)}`
    };
    
    // Create HTML content for the booking
    const htmlContent = `
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset="UTF-8">
        <title>Travel Booking - ${bookingData.bookingReference}</title>
        <style>
            body { font-family: Arial, sans-serif; margin: 20px; background: white; }
            .booking { border: 2px solid #333; padding: 20px; max-width: 600px; margin: 0 auto; }
            .header { text-align: center; border-bottom: 2px solid #333; padding-bottom: 15px; margin-bottom: 20px; }
            .booking-code { font-size: 24px; font-weight: bold; color: #2563eb; margin: 10px 0; }
            .section { margin: 15px 0; }
            .section h3 { color: #333; border-bottom: 1px solid #ccc; padding-bottom: 5px; }
            .info-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin: 10px 0; }
            .info-item { margin: 5px 0; }
            .label { font-weight: bold; color: #666; }
            .value { color: #333; }
            .qr-section { text-align: center; margin: 20px 0; }
            .qr-code { border: 1px solid #ccc; padding: 10px; display: inline-block; }
            .footer { text-align: center; margin-top: 30px; padding-top: 15px; border-top: 1px solid #ccc; color: #666; }
            @media print { body { margin: 0; } .booking { border: none; box-shadow: none; } }
        </style>
    </head>
    <body>
        <div class="booking">
            <div class="header">
                <h1>ShikaTicket</h1>
                <h2>TRAVEL BOOKING</h2>
                <div class="booking-code">${bookingData.bookingReference}</div>
            </div>
            
            <div class="section">
                <h3>Booking Information</h3>
                <div class="info-grid">
                    <div class="info-item">
                        <span class="label">Booking Reference:</span><br>
                        <span class="value">${bookingData.bookingReference}</span>
                    </div>
                    <div class="info-item">
                        <span class="label">Booking Date:</span><br>
                        <span class="value">${bookingData.bookingDate}</span>
                    </div>
                </div>
            </div>
            
            <div class="section">
                <h3>Travel Details</h3>
                <div class="info-grid">
                    <div class="info-item">
                        <span class="label">Destination:</span><br>
                        <span class="value">${bookingData.destination}</span>
                        <br><small style="color: #666;">${bookingData.destinationDetails}</small>
                    </div>
                    <div class="info-item">
                        <span class="label">Departure Date:</span><br>
                        <span class="value">${bookingData.departureDate}</span>
                    </div>
                    <div class="info-item">
                        <span class="label">Departure Location:</span><br>
                        <span class="value">${bookingData.departureLocation}</span>
                    </div>
                    <div class="info-item">
                        <span class="label">Participants:</span><br>
                        <span class="value">${bookingData.participants} person(s)</span>
                    </div>
                </div>
            </div>
            
            <div class="section">
                <h3>Travel Agency</h3>
                <div class="info-grid">
                    <div class="info-item">
                        <span class="label">Agency:</span><br>
                        <span class="value">${bookingData.agency}</span>
                    </div>
                    <div class="info-item">
                        <span class="label">Contact Person:</span><br>
                        <span class="value">${bookingData.contactPerson}</span>
                    </div>
                    <div class="info-item">
                        <span class="label">Email:</span><br>
                        <span class="value">${bookingData.agencyEmail}</span>
                    </div>
                    <div class="info-item">
                        <span class="label">Phone:</span><br>
                        <span class="value">${bookingData.agencyPhone}</span>
                    </div>
                </div>
            </div>
            
            <div class="section">
                <h3>Payment Information</h3>
                <div class="info-item">
                    <span class="label">Total Amount:</span>
                    <span class="value">${bookingData.totalAmount}</span>
                </div>
            </div>
            
            <div class="qr-section">
                <h3>QR Code for Booking</h3>
                <div class="qr-code">
                    <img src="${bookingData.qrCodeUrl}" alt="QR Code" style="width: 200px; height: 200px;">
                </div>
                <p style="margin-top: 10px; color: #666;">Present this QR code at departure</p>
            </div>
            
            <div class="footer">
                <p><strong>Important:</strong> Please arrive at least 30 minutes before departure time.</p>
                <p>Keep this booking reference safe and present it at departure. You can also show the QR code on your mobile device.</p>
                <p style="margin-top: 15px; color: #999;">Generated on ${new Date().toLocaleString()}</p>
            </div>
        </div>
    </body>
    </html>
    `;
    
    // Create a new window with the HTML content and trigger print
    const printWindow = window.open('', '_blank');
    printWindow.document.write(htmlContent);
    printWindow.document.close();
    
    // Wait for the page to load, then trigger print
    printWindow.onload = function() {
        setTimeout(() => {
            printWindow.print();
            printWindow.close();
        }, 500);
    };
}
</script>
