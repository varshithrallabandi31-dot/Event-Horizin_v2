<div class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8 bg-white p-10 rounded-[2rem] border-2 border-charcoal-100 shadow-xl shadow-charcoal-900/5">
        <div class="text-center">
            <div class="inline-flex items-center justify-center w-16 h-16 rounded-2xl bg-brand-50 text-brand-600 mb-6 border-2 border-brand-100">
                <i data-lucide="user-plus" class="w-8 h-8"></i>
            </div>
            <h2 class="serif-heading text-3xl font-bold text-charcoal-900 mb-2">Complete Your Profile</h2>
            <p class="text-charcoal-500">Just a few more details to get you started.</p>
        </div>

        <form class="mt-8 space-y-6" action="<?php echo BASE_URL; ?>profile/complete" method="POST">
            <div class="space-y-4">
                <div>
                    <label class="block text-xs font-bold text-charcoal-600 uppercase tracking-widest mb-2">Full Name</label>
                    <input type="text" name="name" required value="<?php echo htmlspecialchars($_SESSION['user_name'] ?? ''); ?>" class="w-full px-4 py-3 bg-cream-50 rounded-xl border-2 border-charcoal-200 text-charcoal-900 focus:border-brand-500 outline-none transition font-medium" placeholder="John Doe">
                </div>
                <div>
                    <label class="block text-xs font-bold text-charcoal-600 uppercase tracking-widest mb-2">Email Address</label>
                    <input type="email" name="email" required class="w-full px-4 py-3 bg-cream-50 rounded-xl border-2 border-charcoal-200 text-charcoal-900 focus:border-brand-500 outline-none transition font-medium" placeholder="john@example.com">
                </div>
                <div>
                    <label class="block text-xs font-bold text-charcoal-600 uppercase tracking-widest mb-2">Location</label>
                    <input type="text" name="location" required class="w-full px-4 py-3 bg-cream-50 rounded-xl border-2 border-charcoal-200 text-charcoal-900 focus:border-brand-500 outline-none transition font-medium" placeholder="New York, USA">
                </div>
                <div>
                    <label class="block text-xs font-bold text-charcoal-600 uppercase tracking-widest mb-2">Preferred Event Categories</label>
                    <div class="grid grid-cols-2 gap-2">
                        <?php 
                        $categories = ['Tech', 'Music', 'Art', 'Nightlife', 'Business', 'Sports'];
                        foreach($categories as $cat): 
                        ?>
                        <label class="flex items-center gap-2 p-3 bg-cream-50 rounded-xl border-2 border-charcoal-100 cursor-pointer hover:border-brand-400 transition group">
                            <input type="checkbox" name="interests[]" value="<?php echo $cat; ?>" class="w-4 h-4 rounded border-charcoal-300 text-brand-600 focus:ring-brand-500">
                            <span class="text-sm text-charcoal-600 group-hover:text-charcoal-900 font-medium"><?php echo $cat; ?></span>
                        </label>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>

            <button type="submit" class="group relative w-full flex justify-center py-4 px-4 border border-transparent text-sm font-bold rounded-xl text-white bg-charcoal-900 hover:bg-black focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-charcoal-900 transition transform hover:scale-[1.02] shadow-lg">
                Save & Continue
            </button>
        </form>
    </div>
</div>
