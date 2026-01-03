<div class="min-h-[calc(100vh-64px)] flex items-center justify-center p-4">
    <div class="w-full max-w-lg bg-white rounded-[2rem] overflow-hidden shadow-2xl flex flex-col md:flex-row border-2 border-charcoal-100">
        
        <!-- Ticket Left (QR) -->
        <div class="bg-charcoal-900 md:w-1/2 p-8 flex flex-col items-center justify-center border-r-2 border-dashed border-charcoal-800 relative">
            <!-- Decorative Circles for Perforation -->
            <div class="absolute -top-4 -right-4 w-8 h-8 bg-cream-50 rounded-full hidden md:block"></div>
            <div class="absolute -bottom-4 -right-4 w-8 h-8 bg-cream-50 rounded-full hidden md:block"></div>
            
            <div class="bg-white p-4 rounded-3xl mb-6 shadow-xl shadow-brand-500/10">
                <!-- Generating a QR code via a public API for demo -->
                <img src="https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=TICKET-<?php echo $event['id']; ?>-USER-<?php echo $_SESSION['user_id'] ?? 'MOCK'; ?>" class="w-40 h-40">
            </div>
            
            <p class="text-charcoal-500 text-[10px] uppercase tracking-widest font-bold">Ticket ID: #EV-<?php echo rand(1000, 9999); ?></p>
            <p class="text-brand-500 text-xs mt-2 font-bold animate-pulse flex items-center gap-2">
                <i data-lucide="check-circle" class="w-3 h-3"></i> Validated
            </p>
        </div>

        <!-- Ticket Right (Info) -->
        <div class="md:w-1/2 p-8 flex flex-col justify-between bg-white relative">
            <div>
                <p class="text-brand-600 font-bold text-xs uppercase tracking-widest mb-1"><?php echo htmlspecialchars($event['category']); ?></p>
                <h2 class="text-charcoal-900 text-2xl font-black leading-tight mb-4"><?php echo htmlspecialchars($event['title']); ?></h2>
                
                <div class="space-y-4">
                    <div class="flex items-start gap-3">
                        <i data-lucide="calendar" class="text-charcoal-400 w-5 h-5 flex-shrink-0"></i>
                        <div>
                            <p class="text-charcoal-900 text-sm font-bold"><?php echo date('D, M d, Y', strtotime($event['start_time'])); ?></p>
                            <p class="text-charcoal-500 text-xs"><?php echo date('g:i A', strtotime($event['start_time'])); ?></p>
                        </div>
                    </div>
                    <div class="flex items-start gap-3">
                        <i data-lucide="map-pin" class="text-charcoal-400 w-5 h-5 flex-shrink-0"></i>
                        <p class="text-charcoal-900 text-sm font-medium"><?php echo htmlspecialchars($event['location_name']); ?></p>
                    </div>
                </div>
            </div>

            <div class="mt-8 pt-6 border-t-2 border-charcoal-100 flex items-center justify-between">
                <div>
                    <p class="text-charcoal-400 text-[10px] uppercase font-bold">Attendee</p>
                    <p class="text-charcoal-900 text-sm font-bold max-w-[100px] truncate"><?php echo $_SESSION['user_name'] ?? 'Guest Attendee'; ?></p>
                </div>
                <div class="text-right">
                    <p class="text-charcoal-400 text-[10px] uppercase font-bold">Tier</p>
                    <p class="text-brand-600 text-sm font-bold italic">VIP</p>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="text-center mt-4">
    <button onclick="window.print()" class="text-charcoal-500 hover:text-charcoal-900 transition text-xs flex items-center gap-2 mx-auto font-bold uppercase tracking-widest">
        <i data-lucide="printer" class="w-4 h-4"></i> Print Ticket
    </button>
</div>
