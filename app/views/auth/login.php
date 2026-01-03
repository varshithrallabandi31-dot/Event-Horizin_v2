<div class="min-h-[calc(100vh-64px)] flex items-center justify-center relative overflow-hidden">
    <!-- Background Elements -->
    <div class="absolute top-0 left-0 w-full h-full overflow-hidden -z-10">
        <div class="absolute top-[-10%] left-[-10%] w-96 h-96 bg-brand-600/20 rounded-full blur-3xl"></div>
        <div class="absolute bottom-[-10%] right-[-10%] w-96 h-96 bg-purple-600/20 rounded-full blur-3xl"></div>
    </div>

    <div class="w-full max-w-md bg-white/80 backdrop-blur-xl border border-white/40 p-8 rounded-2xl shadow-xl m-4">
        <div class="text-center mb-8">
            <div class="w-16 h-16 bg-gradient-to-tr from-brand-100 to-brand-200 rounded-2xl flex items-center justify-center mx-auto mb-4 shadow-sm border border-brand-100">
                <i data-lucide="smartphone" class="text-brand-600 w-8 h-8"></i>
            </div>
            <h1 class="text-3xl font-bold text-charcoal-900 mb-2">Welcome Back</h1>
            <p class="text-charcoal-500">Enter your phone number to continue</p>
        </div>

        <?php if(isset($error)): ?>
            <div class="bg-red-50 border border-red-200 text-red-600 p-4 rounded-xl mb-6 text-sm text-center font-medium">
                <?php echo $error; ?>
            </div>
        <?php endif; ?>

        <form action="<?php echo BASE_URL; ?>login" method="POST" class="space-y-6">
            <div>
                <label for="phone" id="input-label" class="block text-sm font-bold text-charcoal-700 mb-2 uppercase tracking-wide">Phone Number</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none" id="input-icon-container">
                        <i data-lucide="phone" class="text-charcoal-400 w-5 h-5"></i>
                    </div>
                    <input type="tel" name="phone" id="phone" placeholder="+1 (555) 000-0000" required
                        class="block w-full pl-12 pr-4 py-3 bg-cream-50 border-2 border-charcoal-100 rounded-xl text-charcoal-900 placeholder-charcoal-400 focus:outline-none focus:border-brand-500 focus:bg-white transition font-medium">
                </div>
                <div class="text-right mt-2">
                    <button type="button" id="toggle-login" class="text-sm text-brand-600 hover:text-brand-700 font-medium hover:underline">
                        Login with Email instead
                    </button>
                </div>
            </div>



            <button type="submit" 
                class="w-full bg-brand-600 hover:bg-brand-700 text-white font-bold py-3.5 rounded-xl shadow-lg shadow-brand-500/30 transition transform hover:scale-[1.02]">
                Send Code
            </button>
            
            <p class="text-center text-xs text-charcoal-400 mt-6">
                By continuing, you agree to our Terms of Service and Privacy Policy.
            </p>
        </form>
    </div>
</div>

<script>
    const toggleBtn = document.getElementById('toggle-login');
    const input = document.getElementById('phone');
    const label = document.getElementById('input-label');
    const iconContainer = document.getElementById('input-icon-container');
    let isPhone = true;

    toggleBtn.addEventListener('click', () => {
        isPhone = !isPhone;
        
        if (isPhone) {
            label.textContent = 'Phone Number';
            input.type = 'tel';
            input.name = 'phone';
            input.placeholder = '+1 (555) 000-0000';
            toggleBtn.textContent = 'Login with Email instead';
            iconContainer.innerHTML = '<i data-lucide="phone" class="text-charcoal-400 w-5 h-5"></i>';
        } else {
            label.textContent = 'Email Address';
            input.type = 'email';
            input.name = 'email';
            input.placeholder = 'you@example.com';
            toggleBtn.textContent = 'Login with Phone instead';
            iconContainer.innerHTML = '<i data-lucide="mail" class="text-charcoal-400 w-5 h-5"></i>';
        }
        
        if (typeof lucide !== 'undefined') {
            lucide.createIcons();
        }
    });
</script>
