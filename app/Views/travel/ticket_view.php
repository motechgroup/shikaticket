<?php /** @var array $ticket */ ?>
<div class="max-w-2xl mx-auto px-4 py-8">
    <div class="text-center mb-6">
        <h1 class="text-2xl font-bold text-blue-400 mb-2">🎫 Travel Ticket</h1>
        <div class="text-sm text-gray-400">Present this ticket at departure</div>
    </div>

    <div class="card p-6 mb-6">
        <!-- Ticket Code and Status -->
        <div class="text-center mb-6">
            <div class="text-3xl font-bold tracking-widest text-blue-400 mb-2">
                #<?php echo htmlspecialchars($ticket['ticket_code']); ?>
            </div>
            <?php $status = strtolower($ticket['status'] ?? 'valid'); ?>
            <?php if ($status === 'redeemed'): ?>
                <span class="badge bg-red-600">Redeemed</span>
            <?php else: ?>
                <span class="badge bg-green-600">Valid</span>
            <?php endif; ?>
        </div>

        <!-- QR Code -->
        <div class="flex justify-center mb-6">
            <div class="bg-white p-4 rounded-lg">
                <img src="<?php echo htmlspecialchars($qrUrl); ?>" 
                     alt="QR Code for <?php echo htmlspecialchars($ticket['ticket_code']); ?>" 
                     class="w-64 h-64">
            </div>
        </div>

        <!-- Travel Details -->
        <div class="grid md:grid-cols-2 gap-6">
            <div>
                <h3 class="font-semibold text-blue-300 mb-3">📍 Travel Details</h3>
                <div class="space-y-2 text-sm">
                    <div>
                        <span class="text-gray-400">Destination:</span>
                        <div class="font-medium"><?php echo htmlspecialchars($ticket['destination_title']); ?></div>
                    </div>
                    <div>
                        <span class="text-gray-400">Departure Date:</span>
                        <div class="font-medium"><?php echo date('M j, Y', strtotime($ticket['departure_date'])); ?></div>
                    </div>
                    <div>
                        <span class="text-gray-400">Departure Location:</span>
                        <div class="font-medium"><?php echo htmlspecialchars($ticket['departure_location'] ?? 'TBA'); ?></div>
                    </div>
                    <div>
                        <span class="text-gray-400">Participants:</span>
                        <div class="font-medium"><?php echo (int)$ticket['participants_count']; ?> person(s)</div>
                    </div>
                </div>
            </div>

            <div>
                <h3 class="font-semibold text-blue-300 mb-3">👤 Passenger Info</h3>
                <div class="space-y-2 text-sm">
                    <div>
                        <span class="text-gray-400">Email:</span>
                        <div class="font-medium"><?php echo htmlspecialchars($ticket['user_email']); ?></div>
                    </div>
                    <div>
                        <span class="text-gray-400">Phone:</span>
                        <div class="font-medium"><?php echo htmlspecialchars($ticket['user_phone']); ?></div>
                    </div>
                    <div>
                        <span class="text-gray-400">Booking Reference:</span>
                        <div class="font-medium"><?php echo htmlspecialchars($ticket['booking_reference'] ?? 'TB' . $ticket['id']); ?></div>
                    </div>
                    <div>
                        <span class="text-gray-400">Booking Date:</span>
                        <div class="font-medium"><?php echo date('M j, Y H:i', strtotime($ticket['booking_date'])); ?></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Travel Agency Contact -->
    <div class="card p-6 mb-6">
        <h3 class="font-semibold text-blue-300 mb-3">🏢 Travel Agency</h3>
        <div class="grid md:grid-cols-2 gap-4 text-sm">
            <div>
                <span class="text-gray-400">Agency:</span>
                <div class="font-medium"><?php echo htmlspecialchars($ticket['company_name']); ?></div>
            </div>
            <div>
                <span class="text-gray-400">Contact Person:</span>
                <div class="font-medium"><?php echo htmlspecialchars($ticket['contact_person']); ?></div>
            </div>
            <div>
                <span class="text-gray-400">Email:</span>
                <div class="font-medium"><?php echo htmlspecialchars($ticket['agency_email']); ?></div>
            </div>
            <div>
                <span class="text-gray-400">Phone:</span>
                <div class="font-medium"><?php echo htmlspecialchars($ticket['agency_phone']); ?></div>
            </div>
        </div>
    </div>

    <!-- Payment Information -->
    <?php if (!empty($ticket['total_amount'])): ?>
    <div class="card p-6 mb-6">
        <h3 class="font-semibold text-blue-300 mb-3">💳 Payment Details</h3>
        <div class="text-sm">
            <span class="text-gray-400">Total Amount:</span>
            <div class="font-medium text-lg"><?php echo htmlspecialchars($ticket['currency']); ?> <?php echo number_format((float)$ticket['total_amount'], 2); ?></div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Action Buttons -->
    <div class="text-center space-y-3">
        <button onclick="downloadTicket()" class="btn btn-primary w-full md:w-auto">
            📄 Download Ticket
        </button>
        <button onclick="downloadQR()" class="btn btn-secondary w-full md:w-auto">
            📱 Download QR Code
        </button>
        <a href="<?php echo base_url('/travel'); ?>" class="btn btn-secondary w-full md:w-auto">
            🏠 Back to Travel
        </a>
    </div>

    <!-- Important Notes -->
    <div class="card p-4 mt-6 bg-blue-900/20 border border-blue-600/30">
        <div class="text-sm text-blue-300">
            <div class="font-semibold mb-2">📋 Important Notes:</div>
            <ul class="space-y-1 text-blue-400">
                <li>• Arrive at least 30 minutes before departure time</li>
                <li>• Present this ticket or QR code at the departure location</li>
                <li>• Keep this ticket safe until your travel is complete</li>
                <li>• Contact the travel agency if you have any questions</li>
            </ul>
        </div>
    </div>
</div>

<script>
function downloadTicket() {
    // Create a comprehensive ticket PDF content
    const ticketData = {
        ticketCode: '<?php echo $ticket['ticket_code']; ?>',
        destination: '<?php echo htmlspecialchars($ticket['destination_title']); ?>',
        departureDate: '<?php echo date('M j, Y', strtotime($ticket['departure_date'])); ?>',
        departureLocation: '<?php echo htmlspecialchars($ticket['departure_location'] ?? 'TBA'); ?>',
        participants: '<?php echo (int)$ticket['participants_count']; ?>',
        userEmail: '<?php echo htmlspecialchars($ticket['user_email']); ?>',
        userPhone: '<?php echo htmlspecialchars($ticket['user_phone']); ?>',
        bookingReference: '<?php echo htmlspecialchars($ticket['booking_reference'] ?? 'TB' . $ticket['id']); ?>',
        bookingDate: '<?php echo date('M j, Y H:i', strtotime($ticket['booking_date'])); ?>',
        agencyName: '<?php echo htmlspecialchars($ticket['company_name']); ?>',
        contactPerson: '<?php echo htmlspecialchars($ticket['contact_person']); ?>',
        agencyEmail: '<?php echo htmlspecialchars($ticket['agency_email']); ?>',
        agencyPhone: '<?php echo htmlspecialchars($ticket['agency_phone']); ?>',
        totalAmount: '<?php echo htmlspecialchars($ticket['currency']); ?> <?php echo number_format((float)$ticket['total_amount'], 2); ?>',
        qrCodeUrl: '<?php echo $qrUrl; ?>'
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
            .ticket-code { font-size: 32px; font-weight: bold; color: #2563eb; margin: 10px 0; letter-spacing: 4px; }
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
                <h2>🎫 TRAVEL TICKET</h2>
                <div class="ticket-code">${ticketData.ticketCode}</div>
            </div>
            
            <div class="section">
                <h3>Travel Details</h3>
                <div class="info-grid">
                    <div class="info-item">
                        <span class="label">Destination:</span><br>
                        <span class="value">${ticketData.destination}</span>
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
                <h3>Passenger Information</h3>
                <div class="info-grid">
                    <div class="info-item">
                        <span class="label">Email:</span><br>
                        <span class="value">${ticketData.userEmail}</span>
                    </div>
                    <div class="info-item">
                        <span class="label">Phone:</span><br>
                        <span class="value">${ticketData.userPhone}</span>
                    </div>
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
                <h3>Travel Agency</h3>
                <div class="info-grid">
                    <div class="info-item">
                        <span class="label">Agency:</span><br>
                        <span class="value">${ticketData.agencyName}</span>
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

function downloadQR() {
    const qrUrl = '<?php echo $qrUrl; ?>';
    
    fetch(qrUrl)
        .then(response => response.blob())
        .then(blob => {
            const url = window.URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = `travel-qr-<?php echo $ticket['ticket_code']; ?>.png`;
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
</script>
