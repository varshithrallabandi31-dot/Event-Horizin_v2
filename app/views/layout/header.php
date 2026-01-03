<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EventHorizons - Discover & Host</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&family=Playfair+Display:ital,wght@0,400;0,500;0,600;0,700;1,400&display=swap" rel="stylesheet">
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Outfit', 'sans-serif'],
                        serif: ['Playfair Display', 'serif'],
                    },
                    colors: {
                        brand: {
                            50: '#FDFBF7', 
                            100: '#F9F5EB',
                            200: '#F0EAD6', 
                            300: '#EADBC8',
                            400: '#DAC0A3', 
                            500: '#C19A6B', 
                            600: '#A67F48', 
                            700: '#8B6535',
                            800: '#6F4E28',
                            900: '#483216',
                        },
                        cream: {
                            50: '#FFFCF5', 
                            100: '#FDFBF7',
                            200: '#F4F1EA',
                        },
                        charcoal: {
                            50: '#F9FAFB',
                            100: '#F3F4F6',
                            200: '#E5E7EB',
                            300: '#D1D5DB',
                            400: '#9CA3AF',
                            500: '#6B7280',
                            600: '#4B5563',
                            700: '#374151',
                            800: '#1F2937', 
                            900: '#111827', 
                            950: '#030712',
                        },
                    }
                }
            }
        }
    </script>

    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>
    <script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>

    <style>
        body {
            font-family: 'Outfit', sans-serif;
            transition: background-color 0.3s, color 0.3s;
        }
        .glass-nav {
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(16px);
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
            transition: background-color 0.3s, border-color 0.3s;
        }
        .dark .glass-nav {
            background: rgba(17, 24, 39, 0.85);
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
        }
    </style>
</head>
<body class="antialiased min-h-screen flex flex-col selection:bg-brand-500 selection:text-white bg-cream-50 text-charcoal-900 dark:bg-charcoal-950 dark:text-gray-100"
      x-data="{ 
          darkMode: localStorage.getItem('theme') === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches),
          toggleTheme() {
              this.darkMode = !this.darkMode;
              localStorage.setItem('theme', this.darkMode ? 'dark' : 'light');
              if (this.darkMode) {
                  document.documentElement.classList.add('dark');
              } else {
                  document.documentElement.classList.remove('dark');
              }
          }
      }"
      x-init="$watch('darkMode', val => val ? document.documentElement.classList.add('dark') : document.documentElement.classList.remove('dark')); if(darkMode) document.documentElement.classList.add('dark');"
>

<nav class="glass-nav fixed w-full z-50 top-0 left-0 transition-all duration-300" x-data="{ mobileOpen: false }">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-16">
            <!-- Logo -->
            <div class="flex-shrink-0 flex items-center gap-2 cursor-pointer" onclick="window.location.href='<?php echo BASE_URL; ?>'">
                <div class="w-8 h-8 rounded-lg bg-gradient-to-tr from-brand-300 to-brand-600 flex items-center justify-center shadow-lg shadow-brand-500/20">
                   <i data-lucide="sparkles" class="text-white w-5 h-5"></i>
                </div>
                <span class="font-bold text-xl tracking-tight text-charcoal-900 dark:text-white">Event<span class="text-brand-600 dark:text-brand-500">Horizons</span></span>
            </div>

            <!-- Desktop Menu -->
            <div class="hidden md:block">
                <div class="ml-10 flex items-baseline space-x-1">
                    <a href="<?php echo BASE_URL; ?>" class="text-charcoal-600 dark:text-charcoal-300 hover:text-brand-600 dark:hover:text-brand-400 hover:bg-brand-50 dark:hover:bg-charcoal-800 px-4 py-2 rounded-full text-sm font-bold transition">Home</a>
                    <a href="<?php echo BASE_URL; ?>explore" class="text-charcoal-600 dark:text-charcoal-300 hover:text-brand-600 dark:hover:text-brand-400 hover:bg-brand-50 dark:hover:bg-charcoal-800 px-4 py-2 rounded-full text-sm font-bold transition">Explore</a>
                    <?php if(isset($_SESSION['user_id'])): ?>
                        <a href="<?php echo BASE_URL; ?>organizer/dashboard" class="text-charcoal-600 dark:text-charcoal-300 hover:text-brand-600 dark:hover:text-brand-400 hover:bg-brand-50 dark:hover:bg-charcoal-800 px-4 py-2 rounded-full text-sm font-bold transition">Dashboard</a>
                    <?php endif; ?>
                    <a href="<?php echo BASE_URL; ?>events/create" class="text-charcoal-600 dark:text-charcoal-300 hover:text-brand-600 dark:hover:text-brand-400 hover:bg-brand-50 dark:hover:bg-charcoal-800 px-4 py-2 rounded-full text-sm font-bold transition">Create Event</a>
                </div>
            </div>

            <!-- Auth Buttons & Theme Toggle -->
            <div class="hidden md:flex items-center gap-3">
                 <!-- Theme Toggle -->
                 <button @click="toggleTheme()" class="p-2 rounded-full text-charcoal-500 dark:text-charcoal-400 hover:bg-gray-100 dark:hover:bg-charcoal-800 transition">
                    <i x-show="!darkMode" data-lucide="moon" class="w-5 h-5"></i>
                    <i x-show="darkMode" data-lucide="sun" class="w-5 h-5"></i>
                 </button>

                 <?php if(isset($_SESSION['user_id'])): ?>
                    <a href="<?php echo BASE_URL; ?>profile" class="flex items-center gap-2 text-charcoal-600 dark:text-charcoal-300 hover:text-brand-600 transition">
                        <?php if(isset($_SESSION['user_avatar']) && !empty($_SESSION['user_avatar'])): ?>
                            <img src="<?php echo $_SESSION['user_avatar']; ?>" alt="<?php echo htmlspecialchars($_SESSION['user_name'] ?? 'User'); ?>" class="w-9 h-9 rounded-full border-2 border-brand-200 dark:border-charcoal-700 p-0.5 object-cover">
                        <?php else: ?>
                            <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($_SESSION['user_name'] ?? 'User'); ?>&background=random" class="w-9 h-9 rounded-full border-2 border-brand-200 dark:border-charcoal-700 p-0.5">
                        <?php endif; ?>
                    </a>
                    <a href="<?php echo BASE_URL; ?>logout" class="bg-white dark:bg-charcoal-800 hover:bg-cream-100 dark:hover:bg-charcoal-700 text-charcoal-700 dark:text-gray-200 px-5 py-2.5 rounded-full text-sm font-bold transition border-2 border-charcoal-200 dark:border-charcoal-700">Logout</a>
                <?php else: ?>
                    <a href="<?php echo BASE_URL; ?>login" class="text-charcoal-600 dark:text-charcoal-300 hover:text-brand-600 px-4 py-2 text-sm font-bold transition">Log In</a>
                    <a href="<?php echo BASE_URL; ?>login" class="bg-brand-600 hover:bg-brand-700 text-white px-6 py-2.5 rounded-full text-sm font-bold shadow-lg shadow-brand-500/30 transition transform hover:scale-105">Sign Up</a>
                <?php endif; ?>
            </div>

            <!-- Mobile menu button -->
            <div class="-mr-2 flex md:hidden items-center gap-4">
                <button @click="toggleTheme()" class="p-2 rounded-full text-charcoal-500 dark:text-charcoal-400">
                    <i x-show="!darkMode" data-lucide="moon" class="w-5 h-5"></i>
                    <i x-show="darkMode" data-lucide="sun" class="w-5 h-5"></i>
                </button>
                <button @click="mobileOpen = !mobileOpen" type="button" class="inline-flex items-center justify-center p-2 rounded-md text-charcoal-600 dark:text-charcoal-300 hover:text-brand-600 hover:bg-brand-50 dark:hover:bg-charcoal-800 focus:outline-none transition">
                    <span class="sr-only">Open main menu</span>
                     <i x-show="!mobileOpen" data-lucide="menu" class="block h-6 w-6"></i>
                     <i x-show="mobileOpen" data-lucide="x" class="block h-6 w-6"></i>
                </button>
            </div>
        </div>
    </div>

    <!-- Mobile Menu -->
    <div x-show="mobileOpen" x-transition class="md:hidden bg-white/95 dark:bg-charcoal-900/95 backdrop-blur-xl border-b border-charcoal-100 dark:border-charcoal-800 shadow-xl">
        <div class="px-4 pt-4 pb-6 space-y-2">
            <a href="<?php echo BASE_URL; ?>" class="text-charcoal-600 dark:text-charcoal-300 hover:text-brand-600 hover:bg-brand-50 dark:hover:bg-charcoal-800 block px-3 py-3 rounded-xl text-base font-bold transition">Home</a>
            <a href="<?php echo BASE_URL; ?>explore" class="text-charcoal-600 dark:text-charcoal-300 hover:text-brand-600 hover:bg-brand-50 dark:hover:bg-charcoal-800 block px-3 py-3 rounded-xl text-base font-bold transition">Explore</a>
             <a href="<?php echo BASE_URL; ?>events/create" class="text-charcoal-600 dark:text-charcoal-300 hover:text-brand-600 hover:bg-brand-50 dark:hover:bg-charcoal-800 block px-3 py-3 rounded-xl text-base font-bold transition">Create Event</a>
             <?php if(isset($_SESSION['user_id'])): ?>
                <a href="<?php echo BASE_URL; ?>logout" class="text-charcoal-600 dark:text-charcoal-300 hover:text-red-600 hover:bg-red-50 dark:hover:bg-red-900/20 block px-3 py-3 rounded-xl text-base font-bold transition">Logout</a>
             <?php else: ?>
                <div class="pt-4 mt-4 border-t border-charcoal-100 dark:border-charcoal-800 grid grid-cols-2 gap-4">
                    <a href="<?php echo BASE_URL; ?>login" class="flex justify-center items-center px-4 py-3 rounded-xl border-2 border-charcoal-200 dark:border-charcoal-700 text-charcoal-700 dark:text-charcoal-300 font-bold hover:bg-charcoal-50 dark:hover:bg-charcoal-800 transition">Log In</a>
                    <a href="<?php echo BASE_URL; ?>login" class="flex justify-center items-center px-4 py-3 rounded-xl bg-brand-600 text-white font-bold hover:bg-brand-700 shadow-lg shadow-brand-500/20 transition">Sign Up</a>
                </div>
             <?php endif; ?>
        </div>
    </div>
</nav>

<!-- Global Toast Notification System -->
<div x-data="toastManager()" @toast.window="showToast($event.detail)" class="fixed top-20 right-4 z-[100] space-y-3">
    <template x-for="toast in toasts" :key="toast.id">
        <div x-show="toast.show" 
             x-transition:enter="transition ease-out duration-300 transform"
             x-transition:enter-start="translate-x-full opacity-0"
             x-transition:enter-end="translate-x-0 opacity-100"
             x-transition:leave="transition ease-in duration-200 transform"
             x-transition:leave-start="translate-x-0 opacity-100"
             x-transition:leave-end="translate-x-full opacity-0"
             :class="{
                 'bg-green-50 dark:bg-green-900/30 border-green-500': toast.type === 'success',
                 'bg-red-50 dark:bg-red-900/30 border-red-500': toast.type === 'error',
                 'bg-blue-50 dark:bg-blue-900/30 border-blue-500': toast.type === 'info',
                 'bg-yellow-50 dark:bg-yellow-900/30 border-yellow-500': toast.type === 'warning'
             }"
             class="min-w-[320px] max-w-md p-4 rounded-xl border-l-4 shadow-2xl backdrop-blur-xl flex items-start gap-3 cursor-pointer hover:scale-105 transition-transform"
             @click="removeToast(toast.id)">
            <!-- Icon -->
            <div :class="{
                     'text-green-600 dark:text-green-400': toast.type === 'success',
                     'text-red-600 dark:text-red-400': toast.type === 'error',
                     'text-blue-600 dark:text-blue-400': toast.type === 'info',
                     'text-yellow-600 dark:text-yellow-400': toast.type === 'warning'
                 }">
                <i x-show="toast.type === 'success'" data-lucide="check-circle" class="w-6 h-6"></i>
                <i x-show="toast.type === 'error'" data-lucide="x-circle" class="w-6 h-6"></i>
                <i x-show="toast.type === 'info'" data-lucide="info" class="w-6 h-6"></i>
                <i x-show="toast.type === 'warning'" data-lucide="alert-triangle" class="w-6 h-6"></i>
            </div>
            
            <!-- Content -->
            <div class="flex-1">
                <h4 x-text="toast.title" 
                    :class="{
                        'text-green-900 dark:text-green-100': toast.type === 'success',
                        'text-red-900 dark:text-red-100': toast.type === 'error',
                        'text-blue-900 dark:text-blue-100': toast.type === 'info',
                        'text-yellow-900 dark:text-yellow-100': toast.type === 'warning'
                    }"
                    class="font-bold text-sm mb-1"></h4>
                <p x-text="toast.message" 
                   :class="{
                       'text-green-700 dark:text-green-300': toast.type === 'success',
                       'text-red-700 dark:text-red-300': toast.type === 'error',
                       'text-blue-700 dark:text-blue-300': toast.type === 'info',
                       'text-yellow-700 dark:text-yellow-300': toast.type === 'warning'
                   }"
                   class="text-xs"></p>
            </div>
            
            <!-- Close Button -->
            <button @click.stop="removeToast(toast.id)" 
                    :class="{
                        'text-green-600 hover:text-green-800 dark:text-green-400': toast.type === 'success',
                        'text-red-600 hover:text-red-800 dark:text-red-400': toast.type === 'error',
                        'text-blue-600 hover:text-blue-800 dark:text-blue-400': toast.type === 'info',
                        'text-yellow-600 hover:text-yellow-800 dark:text-yellow-400': toast.type === 'warning'
                    }"
                    class="transition">
                <i data-lucide="x" class="w-4 h-4"></i>
            </button>
        </div>
    </template>
</div>

<script>
function toastManager() {
    return {
        toasts: [],
        nextId: 1,
        
        showToast(config) {
            const toast = {
                id: this.nextId++,
                type: config.type || 'info',
                title: config.title || this.getDefaultTitle(config.type),
                message: config.message || '',
                show: false,
                duration: config.duration || 5000
            };
            
            this.toasts.push(toast);
            
            // Trigger animation
            setTimeout(() => {
                toast.show = true;
                // Refresh Lucide icons
                if (typeof lucide !== 'undefined') lucide.createIcons();
            }, 10);
            
            // Auto remove
            if (toast.duration > 0) {
                setTimeout(() => {
                    this.removeToast(toast.id);
                }, toast.duration);
            }
        },
        
        removeToast(id) {
            const toast = this.toasts.find(t => t.id === id);
            if (toast) {
                toast.show = false;
                setTimeout(() => {
                    this.toasts = this.toasts.filter(t => t.id !== id);
                }, 300);
            }
        },
        
        getDefaultTitle(type) {
            const titles = {
                success: 'Success!',
                error: 'Error',
                info: 'Info',
                warning: 'Warning'
            };
            return titles[type] || 'Notification';
        }
    }
}

// Helper function to trigger toasts from anywhere
window.showToast = function(type, message, title = null, duration = 5000) {
    window.dispatchEvent(new CustomEvent('toast', {
        detail: { type, message, title, duration }
    }));
};
</script>

<main class="flex-grow pt-16">
