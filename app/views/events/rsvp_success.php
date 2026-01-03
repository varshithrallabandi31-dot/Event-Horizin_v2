<?php include __DIR__ . '/../layout/header.php'; ?>

<div class="min-h-screen flex items-center justify-center bg-cream-50 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8 bg-white p-10 rounded-3xl shadow-xl text-center border-2 border-charcoal-200">
        
        <!-- Animated Checkmark -->
        <div class="mx-auto flex items-center justify-center h-24 w-24 rounded-full bg-green-100 mb-6">
            <i data-lucide="check" class="h-12 w-12 text-green-600 animate-bounce"></i>
        </div>

        <h2 class="mt-6 text-3xl font-extrabold text-charcoal-900 serif-heading">
            RSVP Successful!
        </h2>
        <p class="mt-2 text-lg text-charcoal-600">
            You're on the list. We've sent a confirmation to your email.
        </p>

        <div class="mt-8 space-y-3">
            <?php if (!empty($event)): 
                $start = date('Ymd\THis', strtotime($event['start_time']));
                $end = $event['end_time'] ? date('Ymd\THis', strtotime($event['end_time'])) : date('Ymd\THis', strtotime($event['start_time'] . ' +2 hours'));
                $googleLink = "https://calendar.google.com/calendar/render?action=TEMPLATE&text=" . urlencode($event['title']) . "&dates=" . $start . "/" . $end . "&details=" . urlencode($event['description']) . "&location=" . urlencode($event['location_name']);
            ?>
            <a href="<?= $googleLink ?>" target="_blank" class="w-full flex items-center justify-center px-8 py-3 border border-charcoal-200 text-base font-bold rounded-xl text-charcoal-700 bg-white hover:bg-gray-50 transition gap-2">
                <i data-lucide="calendar" class="w-5 h-5"></i> Add to Google Calendar
            </a>
            <?php endif; ?>

            <a href="<?= BASE_URL ?>explore" class="w-full flex items-center justify-center px-8 py-4 border border-transparent text-base font-medium rounded-xl text-white bg-brand-600 hover:bg-brand-700 md:text-lg md:px-10 transition transform hover:scale-105 shadow-lg">
                Return to Main Menu
            </a>
            <p class="mt-4 text-sm text-charcoal-400">
                <a href="<?= BASE_URL ?>profile" class="underline hover:text-brand-600">View My Tickets</a>
            </p>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../layout/footer.php'; ?>
