</main>

<footer class="bg-dark-800 border-t border-gray-800 pt-12 pb-8 mt-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-8 mb-8">
            <div class="col-span-1 md:col-span-1">
                <div class="flex items-center gap-2 mb-4">
                     <div class="w-8 h-8 rounded-lg bg-gradient-to-tr from-brand-300 to-brand-600 flex items-center justify-center shadow-lg shadow-brand-500/20">
                        <i data-lucide="sparkles" class="text-white w-5 h-5"></i>
                    </div>
                    <span class="font-bold text-xl tracking-tight text-white">Event<span class="text-brand-500">Horizons</span></span>
                </div>
                <p class="text-gray-400 text-sm leading-relaxed">
                    The next generation platform for discovering events, connecting with communities, and hosting unforgettable experiences.
                </p>
            </div>
            
            <div>
                <h3 class="text-white font-semibold mb-4">Platform</h3>
                <ul class="space-y-2 text-sm text-gray-400">
                    <li><a href="<?php echo BASE_URL; ?>explore" class="hover:text-brand-500 transition">Browse Events</a></li>
                    <li><a href="<?php echo BASE_URL; ?>events/create" class="hover:text-brand-500 transition">Host an Event</a></li>
                    <li><a href="#" class="hover:text-brand-500 transition">Pricing</a></li>
                </ul>
            </div>

            <div>
                <h3 class="text-white font-semibold mb-4">Community</h3>
                <ul class="space-y-2 text-sm text-gray-400">
                    <li><a href="#" class="hover:text-brand-500 transition">Guidelines</a></li>
                    <li><a href="#" class="hover:text-brand-500 transition">Support</a></li>
                    <li><a href="#" class="hover:text-brand-500 transition">Newsletter</a></li>
                </ul>
            </div>

             <div>
                <h3 class="text-white font-semibold mb-4">Legal</h3>
                <ul class="space-y-2 text-sm text-gray-400">
                    <li><a href="#" class="hover:text-brand-500 transition">Privacy</a></li>
                    <li><a href="#" class="hover:text-brand-500 transition">Terms</a></li>
                </ul>
            </div>
        </div>
        
        <div class="border-t border-gray-700 pt-8 flex flex-col md:flex-row justify-between items-center gap-4">
            <p class="text-gray-500 text-sm">© <?php echo date('Y'); ?> EventHorizons Inc. All rights reserved.</p>
            <div class="flex space-x-4">
                <a href="#" class="text-gray-400 hover:text-white transition"><i data-lucide="twitter" class="w-5 h-5"></i></a>
                <a href="#" class="text-gray-400 hover:text-white transition"><i data-lucide="instagram" class="w-5 h-5"></i></a>
                <a href="#" class="text-gray-400 hover:text-white transition"><i data-lucide="github" class="w-5 h-5"></i></a>
            </div>
        </div>
    </div>
</footer>

<?php 
// Include chatbot component on all pages
include __DIR__ . '/../components/chatbot.php'; 
?>

<!-- Initialize Icons -->
<script>
    lucide.createIcons();
</script>




</body>
</html>
