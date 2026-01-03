<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <div class="mb-10 text-center">
        <h1 class="serif-heading text-4xl font-bold text-charcoal-900 mb-3">Explore Events</h1>
        <p class="text-charcoal-500">Discover what's happening in the community</p>
    </div>

    <!-- Search & Filter Bar -->
    <div class="flex flex-col md:flex-row gap-4 mb-10">
        <div class="relative flex-grow">
            <i data-lucide="search" class="absolute left-4 top-3.5 text-charcoal-400 w-5 h-5"></i>
            <input type="text" placeholder="Search experiences, cities, or vibes..." 
                class="w-full bg-white border-2 border-charcoal-200 rounded-2xl py-3.5 pl-12 pr-4 text-charcoal-900 focus:outline-none focus:border-brand-500 transition shadow-sm">
        </div>
        <div class="flex gap-2 overflow-x-auto pb-2 scrollbar-hide md:pb-0">
            <button class="px-6 py-2 bg-brand-500 text-white text-sm font-bold rounded-full whitespace-nowrap shadow-lg shadow-brand-500/20 hover:bg-brand-600 transition">All</button>
            <button class="px-6 py-2 bg-white text-charcoal-600 text-sm font-bold rounded-full hover:bg-cream-50 hover:text-brand-600 whitespace-nowrap transition border-2 border-charcoal-200">Tech</button>
            <button class="px-6 py-2 bg-white text-charcoal-600 text-sm font-bold rounded-full hover:bg-cream-50 hover:text-brand-600 whitespace-nowrap transition border-2 border-charcoal-200">Music</button>
            <button class="px-6 py-2 bg-white text-charcoal-600 text-sm font-bold rounded-full hover:bg-cream-50 hover:text-brand-600 whitespace-nowrap transition border-2 border-charcoal-200">Social</button>
        </div>
    </div>

    <!-- Event Grid -->
    <?php if(empty($events)): ?>
        <div class="text-center py-20 bg-cream-50 rounded-3xl border-2 border-dashed border-charcoal-200">
            <div class="w-16 h-16 bg-brand-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <i data-lucide="calendar-x" class="text-brand-500 w-8 h-8"></i>
            </div>
            <h3 class="text-xl font-bold text-charcoal-900 mb-2">No events found</h3>
            <p class="text-charcoal-500">Try adjusting your filters or search terms</p>
        </div>
    <?php else: ?>
        <div x-data="{ loading: true }" x-init="setTimeout(() => loading = false, 800)">
            <!-- Skeleton Grid -->
            <div x-show="loading" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                <?php for($i=0; $i<8; $i++): ?>
                    <?php include __DIR__ . '/../components/Skeleton.php'; ?>
                <?php endfor; ?>
            </div>

            <!-- Actual Content -->
            <div x-show="!loading" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6" style="display: none;">
                <?php foreach($events as $event): ?>
                    <a href="<?php echo BASE_URL; ?>event/<?php echo $event['id']; ?>" class="group bg-white dark:bg-charcoal-900 rounded-3xl overflow-hidden border-2 border-charcoal-200 dark:border-charcoal-800 hover:border-brand-400 dark:hover:border-brand-600 transition-all duration-300 hover:shadow-xl hover:shadow-brand-500/10 hover:-translate-y-1">
                        <div class="relative h-48 overflow-hidden">
                            <img src="<?php echo $event['image_url'] ?: 'https://via.placeholder.com/600x400'; ?>" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700">
                            <div class="absolute top-3 left-3 px-3 py-1 bg-white/90 dark:bg-charcoal-900/90 backdrop-blur-md rounded-lg text-[10px] font-bold text-charcoal-900 dark:text-white uppercase tracking-wider border border-white/20 shadow-sm">
                                <?php echo htmlspecialchars($event['category']); ?>
                            </div>
                        </div>
                        <div class="p-5">
                            <h3 class="text-lg font-bold text-charcoal-900 dark:text-white mb-2 line-clamp-1 group-hover:text-brand-600 dark:group-hover:text-brand-400 transition"><?php echo htmlspecialchars($event['title']); ?></h3>
                            <div class="space-y-2">
                                <p class="flex items-center gap-2 text-xs text-brand-600 dark:text-brand-400 font-bold">
                                    <i data-lucide="calendar" class="w-3.5 h-3.5"></i>
                                    <?php echo date('M d, D', strtotime($event['start_time'])); ?> • <?php echo date('g:i A', strtotime($event['start_time'])); ?>
                                </p>
                                <p class="flex items-center gap-2 text-xs text-charcoal-500 dark:text-charcoal-400 truncate">
                                    <i data-lucide="map-pin" class="w-3.5 h-3.5"></i>
                                    <?php echo htmlspecialchars($event['location_name']); ?>
                                </p>
                            </div>
                        </div>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endif; ?>
</div>


<style>
    /* Custom Leaflet Popup Styles */
    .leaflet-popup-content-wrapper {
        background: white;
        border-radius: 1rem;
        padding: 0;
        overflow: hidden;
    }
    .leaflet-popup-content {
        margin: 0;
    }
</style>
