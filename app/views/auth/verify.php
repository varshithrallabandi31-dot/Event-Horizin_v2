<div class="min-h-[calc(100vh-64px)] flex items-center justify-center relative overflow-hidden">
     <!-- Background Elements -->
    <div class="absolute top-0 left-0 w-full h-full overflow-hidden -z-10">
        <div class="absolute top-[-10%] right-[-10%] w-96 h-96 bg-brand-600/20 rounded-full blur-3xl"></div>
        <div class="absolute bottom-[-10%] left-[-10%] w-96 h-96 bg-blue-600/20 rounded-full blur-3xl"></div>
    </div>

    <div class="w-full max-w-md bg-white/80 backdrop-blur-xl border border-white/40 p-8 rounded-2xl shadow-xl m-4">
        <div class="text-center mb-8">
            <div class="w-16 h-16 bg-cream-100 rounded-full flex items-center justify-center mx-auto mb-4 border border-brand-100">
                <i data-lucide="lock" class="text-brand-600 w-8 h-8"></i>
            </div>
            <h1 class="text-3xl font-bold text-charcoal-900 mb-2">Verify it's you</h1>
            <p class="text-charcoal-500">We sent a code to <span class="text-charcoal-900 font-bold"><?php echo htmlspecialchars($phone); ?></span></p>
        <?php if(isset($demo_otp) && (!isset($is_email) || !$is_email)): ?>
            <div class="bg-brand-50 border border-brand-200 text-brand-800 p-4 rounded-xl mb-6 text-center">
                <p class="text-xs uppercase tracking-wider text-brand-500 font-bold mb-1">Developer Mode</p>
                <p>Your verification code is: <span class="text-brand-900 font-bold text-lg tracking-widest"><?php echo $demo_otp; ?></span></p>
            </div>
        <?php endif; ?>

        <?php if(isset($error)): ?>
            <div class="bg-red-50 border border-red-200 text-red-600 p-4 rounded-xl mb-6 text-sm text-center font-medium">
                <?php echo $error; ?>
            </div>
        <?php endif; ?>

        <form action="<?php echo BASE_URL; ?>verify-otp" method="POST" class="space-y-6">
            <div>
                <label for="otp" class="block text-sm font-bold text-charcoal-700 mb-2 text-center uppercase tracking-wide">Enter 6-digit code</label>
                <input type="text" name="otp" id="otp" placeholder="000 000" maxlength="6" required autofocus
                    class="block w-full text-center text-3xl tracking-[0.5em] font-mono py-4 bg-cream-50 border-2 border-charcoal-100 rounded-xl text-charcoal-900 placeholder-charcoal-300 focus:outline-none focus:border-brand-500 focus:bg-white transition uppercase">
            </div>

            <button type="submit" 
                class="w-full bg-charcoal-900 hover:bg-black text-white font-bold py-3.5 rounded-xl shadow-lg transition transform hover:scale-[1.02]">
                Verify & Login
            </button>
            
            <div class="text-center mt-6">
                <a href="<?php echo BASE_URL; ?>login" class="text-sm text-charcoal-500 hover:text-brand-600 font-medium transition">
                    <?php echo (isset($is_email) && $is_email) ? 'Wrong email?' : 'Wrong number?'; ?>
                </a>
            </div>
        </form>
    </div>
</div>
