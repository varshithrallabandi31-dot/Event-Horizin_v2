<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20">
    <div class="flex flex-col md:flex-row items-center justify-between mb-12 gap-6">
        <div>
            <a href="<?php echo BASE_URL; ?>event/<?php echo $event['id']; ?>" class="text-brand-400 hover:text-brand-300 text-sm font-medium flex items-center gap-2 mb-2">
                <i data-lucide="arrow-left" class="w-4 h-4"></i> Back to Event
            </a>
            <h1 class="text-4xl font-bold text-white"><?php echo htmlspecialchars($event['title'] ?? ''); ?> Album</h1>
        </div>
        
        <?php if(isset($_SESSION['user_id'])): ?>
        <form action="<?php echo BASE_URL; ?>event/<?php echo $event['id']; ?>/upload-photo" method="POST" enctype="multipart/form-data" class="flex items-center gap-4">
            <label class="cursor-pointer px-6 py-3 bg-brand-600 text-white rounded-full font-semibold hover:bg-brand-500 transition flex items-center gap-2">
                <i data-lucide="upload" class="w-4 h-4"></i> Upload Photo
                <input type="file" name="photo" class="hidden" onchange="this.form.submit()">
            </label>
        </form>
        <?php endif; ?>
    </div>

    <?php if(empty($photos)): ?>
    <div class="text-center py-20 bg-dark-800 rounded-3xl border border-white/5">
        <i data-lucide="image" class="w-16 h-16 text-gray-600 mx-auto mb-4"></i>
        <h3 class="text-xl font-bold text-gray-400">No photos yet</h3>
        <p class="text-gray-500">Be the first to share a memory!</p>
    </div>
    <?php else: ?>
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
        <?php foreach($photos as $photo): ?>
        <div class="group relative aspect-square bg-dark-800 rounded-2xl overflow-hidden border border-white/10 hover:border-brand-500/50 transition duration-500">
            <img src="<?php echo BASE_URL . $photo['file_path']; ?>" class="w-full h-full object-cover transition duration-700 group-hover:scale-110">
            <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex flex-col justify-end p-4">
                <p class="text-white font-semibold text-sm"><?php echo htmlspecialchars($photo['user_name'] ?? 'Guest'); ?></p>
                <p class="text-gray-400 text-xs"><?php echo date('M d, Y', strtotime($photo['created_at'])); ?></p>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>
</div>
