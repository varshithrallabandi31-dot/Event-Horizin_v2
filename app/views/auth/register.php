<div class="container d-flex align-items-center justify-content-center" style="min-height: 80vh;">
    <div class="w-full max-w-lg bg-white/80 backdrop-blur-xl border border-white/40 p-8 rounded-2xl shadow-xl m-4">
        <div class="text-center mb-6">
            <h2 class="text-3xl font-bold text-charcoal-900">Create Account</h2>
            <p class="text-charcoal-500">Join the community today</p>
        </div>

        <?php if(isset($error)): ?>
            <div class="bg-red-50 border border-red-200 text-red-600 p-4 rounded-xl mb-6 text-sm text-center font-medium">
                <?php echo $error; ?>
            </div>
        <?php endif; ?>

        <form action="/register" method="POST" class="space-y-4">
            <div>
                <label class="block text-sm font-bold text-charcoal-700 mb-1 uppercase tracking-wide">Full Name</label>
                <input type="text" name="full_name" class="w-full px-4 py-3 bg-cream-50 border-2 border-charcoal-100 rounded-xl text-charcoal-900 focus:outline-none focus:border-brand-500 focus:bg-white transition" required>
            </div>
            
            <div>
                <label class="block text-sm font-bold text-charcoal-700 mb-1 uppercase tracking-wide">Username</label>
                <input type="text" name="username" class="w-full px-4 py-3 bg-cream-50 border-2 border-charcoal-100 rounded-xl text-charcoal-900 focus:outline-none focus:border-brand-500 focus:bg-white transition" required>
            </div>
            
            <div>
                <label class="block text-sm font-bold text-charcoal-700 mb-1 uppercase tracking-wide">Email Address</label>
                <input type="email" name="email" class="w-full px-4 py-3 bg-cream-50 border-2 border-charcoal-100 rounded-xl text-charcoal-900 focus:outline-none focus:border-brand-500 focus:bg-white transition" required>
            </div>
            
            <div>
                <label class="block text-sm font-bold text-charcoal-700 mb-1 uppercase tracking-wide">Password</label>
                <input type="password" name="password" class="w-full px-4 py-3 bg-cream-50 border-2 border-charcoal-100 rounded-xl text-charcoal-900 focus:outline-none focus:border-brand-500 focus:bg-white transition" required>
            </div>
            
            <button type="submit" class="w-full bg-brand-600 hover:bg-brand-700 text-white font-bold py-3.5 rounded-xl shadow-lg shadow-brand-500/30 transition transform hover:scale-[1.02] mt-4">
                Create Account
            </button>
            
            <div class="text-center mt-6">
                <a href="/login" class="text-charcoal-500 hover:text-brand-600 text-sm font-medium transition">Already have an account? <span class="text-brand-700 font-bold">Sign in</span></a>
            </div>
        </form>
    </div>
</div>
