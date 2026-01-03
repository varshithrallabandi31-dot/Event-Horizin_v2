<?php
// Generate event schedule based on event type and start time
$eventStartTime = strtotime($event['start_time']);
$eventType = strtolower($event['category'] ?? 'social');

// Define schedules for different event types
$schedules = [
    'music' => [
        ['time' => '-30 minutes', 'title' => 'Arrival & Refreshments'],
        ['time' => '0 minutes', 'title' => 'Welcome & Opening Ritual'],
        ['time' => '+30 minutes', 'title' => 'Main Performance Begins'],
        ['time' => '+90 minutes', 'title' => 'Intermission & Networking'],
        ['time' => '+120 minutes', 'title' => 'Second Set'],
        ['time' => '+180 minutes', 'title' => 'Closing & Photo Moments'],
        ['time' => '+210 minutes', 'title' => 'Event Ends']
    ],
    'tech' => [
        ['time' => '-15 minutes', 'title' => 'Registration & Networking'],
        ['time' => '0 minutes', 'title' => 'Opening Keynote'],
        ['time' => '+45 minutes', 'title' => 'Panel Discussion'],
        ['time' => '+90 minutes', 'title' => 'Break & Refreshments'],
        ['time' => '+105 minutes', 'title' => 'Workshop Sessions'],
        ['time' => '+165 minutes', 'title' => 'Q&A & Networking'],
        ['time' => '+195 minutes', 'title' => 'Closing Remarks']
    ],
    'art' => [
        ['time' => '-20 minutes', 'title' => 'Gallery Opening'],
        ['time' => '0 minutes', 'title' => 'Welcome & Artist Introduction'],
        ['time' => '+30 minutes', 'title' => 'Guided Exhibition Tour'],
        ['time' => '+75 minutes', 'title' => 'Interactive Workshop'],
        ['time' => '+120 minutes', 'title' => 'Artist Meet & Greet'],
        ['time' => '+150 minutes', 'title' => 'Closing Reception']
    ],
    'social' => [
        ['time' => '-30 minutes', 'title' => 'Arrival & Refreshments'],
        ['time' => '0 minutes', 'title' => 'Welcome & Opening Ritual'],
        ['time' => '+30 minutes', 'title' => 'Guided Reflection Activities'],
        ['time' => '+75 minutes', 'title' => 'Sharing Circle'],
        ['time' => '+120 minutes', 'title' => 'Setting Intentions'],
        ['time' => '+150 minutes', 'title' => 'Closing & Photo Moments'],
        ['time' => '+180 minutes', 'title' => 'Event Ends']
    ]
];

$eventSchedule = [];
if (!empty($event['schedule'])) {
    $scheduleData = json_decode($event['schedule'], true);
    if (is_array($scheduleData)) {
        $eventSchedule = $scheduleData;
    }
}

if (empty($eventSchedule)) {
    $eventSchedule = $schedules[$eventType] ?? $schedules['social'];
}
?>

<!-- Countdown Section (Full Screen) -->
<div class="relative h-screen w-full flex items-center justify-center overflow-hidden" id="countdown-section">
    <!-- Background Image -->
    <!-- Background Media -->
    <?php if (isset($event['header_type']) && $event['header_type'] === 'video'): ?>
        <video src="<?php echo $event['image_url']; ?>" autoplay muted loop playsinline class="absolute inset-0 w-full h-full object-cover object-center"></video>
    <?php else: ?>
        <img src="<?php echo $event['image_url'] ?? 'https://via.placeholder.com/1920x1080'; ?>" 
             class="absolute inset-0 w-full h-full object-cover">
    <?php endif; ?>
    
    <!-- Gradient Overlay -->
    <div class="absolute inset-0 bg-gradient-to-b from-black/60 via-black/40 to-black/70"></div>
    
    <!-- Countdown Content -->
    <div class="relative z-10 text-center px-4 max-w-4xl mx-auto">
        <span class="inline-block py-2 px-4 rounded-full bg-white/20 backdrop-blur-md border border-white/30 text-white text-sm font-bold mb-6">
            <?php echo htmlspecialchars($event['category'] ?? ''); ?>
        </span>
        
        <h1 class="serif-heading text-5xl md:text-7xl font-bold text-white mb-8 leading-tight">
            <?php echo htmlspecialchars($event['title'] ?? ''); ?>
        </h1>
        
        <p class="text-xl text-white/90 mb-12 font-light">
            <?php echo date('l, F j, Y • g:i A', strtotime($event['start_time'])); ?>
        </p>
        
        <!-- Countdown Timer -->
        <div class="flex gap-6 justify-center mb-12" x-data="countdown('<?php echo date('Y-m-d H:i:s', strtotime($event['start_time'])); ?>')">
            <div class="text-center">
                <div class="bg-white/10 backdrop-blur-md border border-white/20 rounded-2xl p-6 min-w-[100px]">
                    <h3 class="text-5xl font-bold text-white" x-text="days">00</h3>
                    <p class="text-sm text-white/70 uppercase mt-2 font-semibold">Days</p>
                </div>
            </div>
            <div class="text-center">
                <div class="bg-white/10 backdrop-blur-md border border-white/20 rounded-2xl p-6 min-w-[100px]">
                    <h3 class="text-5xl font-bold text-white" x-text="hours">00</h3>
                    <p class="text-sm text-white/70 uppercase mt-2 font-semibold">Hours</p>
                </div>
            </div>
            <div class="text-center">
                <div class="bg-white/10 backdrop-blur-md border border-white/20 rounded-2xl p-6 min-w-[100px]">
                    <h3 class="text-5xl font-bold text-white" x-text="minutes">00</h3>
                    <p class="text-sm text-white/70 uppercase mt-2 font-semibold">Minutes</p>
                </div>
            </div>
            <div class="text-center">
                <div class="bg-white/10 backdrop-blur-md border border-white/20 rounded-2xl p-6 min-w-[100px]">
                    <h3 class="text-5xl font-bold text-white" x-text="seconds">00</h3>
                    <p class="text-sm text-white/70 uppercase mt-2 font-semibold">Seconds</p>
                </div>
            </div>
        </div>
        
        <!-- Scroll Indicator -->
        <div class="animate-bounce">
            <i data-lucide="chevron-down" class="w-8 h-8 text-white/70"></i>
        </div>
    </div>
</div>

<!-- Event Content Section (Light Theme) -->
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12" x-data="eventPage()">
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-10">
        
        <!-- Left Column: Content -->
        <div class="lg:col-span-2 space-y-6">
            
            <!-- Tab Navigation -->
            <div class="flex items-center gap-2 border-b-2 border-charcoal-100 mb-8 sticky top-0 bg-gray-50/80 backdrop-blur-md z-20 py-4 overflow-x-auto no-scrollbar">
                <?php 
                $tabs = ['About', 'Schedule', 'Venue', 'FAQ'];
                $isOrganizer = (isset($_SESSION['user_id']) && $event['organizer_id'] == $_SESSION['user_id']);
                if ($rsvpStatus === 'approved' || $isOrganizer) {
                    $tabs[] = 'Memories';
                    $tabs[] = 'Community';
                    $tabs[] = 'Refer';
                }
                ?>
                <?php foreach($tabs as $tab): ?>
                    <button @click="selectedTab = '<?php echo strtolower($tab); ?>'; if('<?php echo strtolower($tab); ?>' === 'venue') window.dispatchEvent(new CustomEvent('show-map'))" 
                        :class="selectedTab === '<?php echo strtolower($tab); ?>' ? 'bg-brand-500 text-white shadow-lg' : 'text-charcoal-500 hover:bg-gray-100'"
                        class="px-6 py-2 rounded-xl font-bold transition flex items-center gap-2 whitespace-nowrap">
                        <?php echo $tab; ?>
                    </button>
                <?php endforeach; ?>
                
                <!-- Custom Section Tabs -->
                <?php if (!empty($customSections)): ?>
                    <?php foreach($customSections as $section): ?>
                         <button @click="selectedTab = 'section_<?php echo $section['id'] ?? $section['order']; ?>'" 
                            :class="selectedTab === 'section_<?php echo $section['id'] ?? $section['order']; ?>' ? 'bg-brand-500 text-white shadow-lg' : 'text-charcoal-500 hover:bg-gray-100'"
                            class="px-6 py-2 rounded-xl font-bold transition flex items-center gap-2 whitespace-nowrap">
                            <?php echo htmlspecialchars($section['title']); ?>
                        </button>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

            <!-- Tab Content -->
            
            <!-- About Tab -->
            <div x-show="selectedTab === 'about'" x-transition:enter="transition ease-out duration-300 transform opacity-0 scale-95" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100">
                <div class="bg-white dark:bg-charcoal-900 rounded-3xl p-8 border-2 border-charcoal-200 dark:border-charcoal-800 shadow-sm">
                    <div class="flex items-center justify-between mb-6">
                        <h2 class="serif-heading text-3xl font-bold text-charcoal-900 dark:text-white">About this event</h2>
                        
                        <?php if($isOrganizer): ?>
                        <!-- Edit and Delete Icons (Organizer Only) -->
                        <div class="flex items-center gap-2">
                            <button @click="editingAbout = true" 
                                    x-show="!editingAbout"
                                    class="p-2 hover:bg-brand-50 dark:hover:bg-brand-900/20 rounded-lg transition group"
                                    title="Edit description">
                                <i data-lucide="edit-2" class="w-5 h-5 text-brand-600 dark:text-brand-400 group-hover:scale-110 transition-transform"></i>
                            </button>
                            <button @click="deleteEvent()" 
                                    x-show="!editingAbout"
                                    class="p-2 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-lg transition group"
                                    title="Delete event">
                                <i data-lucide="trash-2" class="w-5 h-5 text-red-600 dark:text-red-400 group-hover:scale-110 transition-transform"></i>
                            </button>
                        </div>
                        <?php endif; ?>
                    </div>
                    
                    <!-- View Mode -->
                    <div x-show="!editingAbout" class="text-charcoal-700 dark:text-charcoal-300 leading-relaxed space-y-4">
                        <?php echo nl2br(htmlspecialchars($event['description'] ?? '')); ?>
                    </div>
                    
                    <?php if($isOrganizer): ?>
                    <!-- Edit Mode (Organizer Only) -->
                    <div x-show="editingAbout" x-cloak>
                        <textarea x-model="aboutDescription" 
                                  rows="8" 
                                  class="w-full bg-cream-50 dark:bg-charcoal-800 border-2 border-charcoal-200 dark:border-charcoal-700 rounded-xl px-4 py-3 text-charcoal-900 dark:text-white focus:border-brand-500 outline-none transition resize-none"
                                  placeholder="Enter event description..."></textarea>
                        
                        <div class="flex items-center justify-between mt-4">
                            <p class="text-xs text-charcoal-500 dark:text-charcoal-400">
                                <span x-text="aboutDescription.length"></span> characters
                            </p>
                            <div class="flex gap-2">
                                <button @click="editingAbout = false; aboutDescription = originalDescription" 
                                        class="px-4 py-2 bg-charcoal-100 dark:bg-charcoal-700 text-charcoal-700 dark:text-charcoal-300 rounded-lg font-semibold hover:bg-charcoal-200 dark:hover:bg-charcoal-600 transition">
                                    Cancel
                                </button>
                                <button @click="saveAboutDescription()" 
                                        :disabled="savingAbout"
                                        class="px-6 py-2 bg-brand-600 text-white rounded-lg font-semibold hover:bg-brand-700 transition disabled:opacity-50 disabled:cursor-not-allowed flex items-center gap-2">
                                    <span x-show="!savingAbout">Save Changes</span>
                                    <span x-show="savingAbout">Saving...</span>
                                    <i data-lucide="check" x-show="!savingAbout" class="w-4 h-4"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>


            <!-- Custom Section Content -->
            <?php if (!empty($customSections)): ?>
                <?php foreach($customSections as $section): ?>
                    <div x-show="selectedTab === 'section_<?php echo $section['id'] ?? $section['order']; ?>'" x-transition>
                        <div class="bg-white rounded-3xl p-8 border-2 border-charcoal-200 shadow-sm">
                            <h2 class="serif-heading text-3xl font-bold text-charcoal-900 mb-6"><?php echo htmlspecialchars($section['title']); ?></h2>
                            <div class="text-charcoal-700 leading-relaxed space-y-4 prose max-w-none">
                                <?php 
                                    // Allow HTML for custom content if trusted, otherwise htmlspecialchars. 
                                    // For now assuming we want rich text so using raw or minimal sanitization.
                                    // Warning: potential XSS if public input. Assuming organizer trusted.
                                    echo $section['content']; 
                                ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>



            <!-- Schedule Tab -->
            <div x-show="selectedTab === 'schedule'" x-transition>
                <div class="bg-white rounded-3xl p-8 border-2 border-charcoal-200 shadow-sm">
                    <div class="flex items-center justify-between mb-8">
                        <h2 class="serif-heading text-3xl font-bold text-charcoal-900">Event Schedule</h2>
                        
                        <?php if($isOrganizer): ?>
                        <div class="flex items-center gap-2">
                             <button @click="editingSchedule = true; scheduleItems = JSON.parse(JSON.stringify(originalSchedule))" 
                                     x-show="!editingSchedule"
                                     class="p-2 hover:bg-brand-50 rounded-lg transition group"
                                     title="Edit schedule">
                                 <i data-lucide="edit-2" class="w-5 h-5 text-brand-600 group-hover:scale-110 transition-transform"></i>
                             </button>
                        </div>
                        <?php endif; ?>
                    </div>

                    <!-- View Mode -->
                    <div x-show="!editingSchedule" class="relative px-4">
                        <div class="absolute left-[29px] top-0 bottom-0 w-0.5 bg-brand-200"></div>
                        <div class="space-y-8">
                            <?php foreach($eventSchedule as $item): 
                                // Handle both relative and absolute times for display
                                if (strpos($item['time'], 'minutes') !== false) {
                                    $itemTime = strtotime($item['time'], $eventStartTime);
                                    $displayTime = date('g:i A', $itemTime);
                                } else {
                                    // Try to parse as absolute time, or if fails, display as is
                                    $ts = strtotime($item['time']);
                                    // If strtotime returns false or it's just a time string, we might need date()
                                    // If we store "9:00 PM", strtotime("9:00 PM") gives today, which is fine for formatting content
                                    $displayTime = $item['time']; // Just display stored string
                                }
                            ?>
                            <div class="relative flex items-start gap-8">
                                <div class="w-8 h-8 rounded-full bg-brand-500 border-4 border-white shadow-md flex-shrink-0 z-10 flex items-center justify-center">
                                    <div class="w-2 h-2 bg-white rounded-full"></div>
                                </div>
                                <div>
                                    <p class="text-brand-600 font-bold text-sm mb-1 uppercase tracking-wider"><?php echo htmlspecialchars($displayTime); ?></p>
                                    <p class="text-charcoal-900 font-bold text-xl"><?php echo htmlspecialchars($item['title']); ?></p>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <!-- Edit Mode -->
                    <div x-show="editingSchedule" x-cloak class="space-y-6">
                        <template x-for="(item, index) in scheduleItems" :key="index">
                            <div class="flex gap-4 items-start p-4 bg-gray-50 rounded-xl border border-gray-200">
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 w-full">
                                    <div>
                                        <label class="block text-xs font-bold text-charcoal-500 mb-1 uppercase">Time</label>
                                        <input type="text" x-model="item.time" placeholder="e.g. 9:00 PM" class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:border-brand-500 focus:ring-2 focus:ring-brand-200 outline-none transition">
                                    </div>
                                    <div class="md:col-span-2">
                                        <label class="block text-xs font-bold text-charcoal-500 mb-1 uppercase">Activity</label>
                                        <input type="text" x-model="item.title" placeholder="Activity Title" class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:border-brand-500 focus:ring-2 focus:ring-brand-200 outline-none transition">
                                    </div>
                                </div>
                                <button @click="scheduleItems = scheduleItems.filter((_, i) => i !== index)" class="mt-6 p-2 text-red-500 hover:bg-red-50 rounded-lg transition" title="Remove item">
                                    <i data-lucide="trash-2" class="w-5 h-5"></i>
                                </button>
                            </div>
                        </template>

                        <button @click="scheduleItems.push({time: '', title: ''})" class="w-full py-3 border-2 border-dashed border-gray-300 rounded-xl text-gray-500 font-bold hover:border-brand-500 hover:text-brand-600 transition flex items-center justify-center gap-2">
                            <i data-lucide="plus" class="w-5 h-5"></i> Add Schedule Item
                        </button>

                        <div class="flex justify-end gap-3 pt-4 border-t border-gray-100">
                            <button @click="editingSchedule = false" class="px-6 py-2 bg-gray-100 text-gray-700 font-bold rounded-xl hover:bg-gray-200 transition">Cancel</button>
                            <button @click="saveSchedule()" 
                                    :disabled="savingSchedule"
                                    class="px-6 py-2 bg-brand-600 text-white font-bold rounded-xl hover:bg-brand-700 transition shadow-lg disabled:opacity-50 flex items-center gap-2">
                                <span x-show="!savingSchedule">Save Schedule</span>
                                <span x-show="savingSchedule">Saving...</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Venue Tab -->
            <div x-show="selectedTab === 'venue'" x-transition @show-map.window="if(!editingVenue) setTimeout(() => initMap(), 100)">
                <div class="bg-white rounded-3xl overflow-hidden border-2 border-charcoal-200 shadow-sm relative">
                    
                    <?php if($isOrganizer): ?>
                    <div class="absolute top-4 right-4 z-40">
                         <button @click="editingVenue = true; initEditMap()" 
                                 x-show="!editingVenue"
                                 class="bg-white/90 backdrop-blur p-2.5 hover:bg-white rounded-xl shadow-lg border-2 border-charcoal-100 group transition"
                                 title="Edit venue">
                             <i data-lucide="edit-2" class="w-5 h-5 text-brand-600 group-hover:scale-110 transition-transform"></i>
                         </button>
                    </div>
                    <?php endif; ?>

                    <!-- View Mode -->
                    <div x-show="!editingVenue">
                        <!-- Leaflet Map Container -->
                        <div id="venueMap" class="h-80 bg-gray-100 relative z-10 grayscale contrast-125"></div>
                        
                        <div class="p-8 relative">
                            <!-- Floating Location Card (matches reference style) -->
                            <div class="absolute -top-16 left-8 bg-white p-4 rounded-xl shadow-xl border-2 border-brand-400 flex items-center gap-4 max-w-xs z-20">
                                <div class="w-10 h-10 bg-brand-50 rounded-lg flex items-center justify-center flex-shrink-0">
                                    <i data-lucide="map-pin" class="text-brand-600 w-6 h-6"></i>
                                </div>
                                <div>
                                    <p class="font-bold text-charcoal-900 leading-tight">
                                        <?php echo htmlspecialchars($event['location_name'] ?? 'The Venue'); ?>
                                        <?php if(!empty($event['city'])): ?>
                                            <span class="block text-xs text-charcoal-500 font-normal mt-0.5"><?php echo htmlspecialchars($event['city']); ?></span>
                                        <?php endif; ?>
                                    </p>
                                    <a href="https://www.google.com/maps/search/?api=1&query=<?php echo urlencode($event['location_name']); ?>" target="_blank" class="text-[10px] text-brand-600 font-bold hover:underline uppercase tracking-widest mt-1 inline-block">Get Directions</a>
                                </div>
                            </div>

                            <div class="mt-8">
                                <h2 class="serif-heading text-3xl font-bold text-charcoal-900 mb-4">The Venue</h2>
                                <p class="text-charcoal-600 mb-6">
                                    <?php echo htmlspecialchars($event['location_name']); ?>
                                    <?php if(!empty($event['city'])): ?>
                                        <span class="text-charcoal-400 mx-2">•</span> <?php echo htmlspecialchars($event['city']); ?>
                                    <?php endif; ?>
                                </p>
                                <div class="grid grid-cols-2 gap-4">
                                    <div class="bg-cream-50 p-4 rounded-2xl border-2 border-charcoal-100">
                                        <p class="text-[10px] text-charcoal-400 uppercase font-bold tracking-widest mb-1">Access</p>
                                        <p class="text-sm font-bold text-charcoal-800">Wheelchair Friendly</p>
                                    </div>
                                    <div class="bg-cream-50 p-4 rounded-2xl border-2 border-charcoal-100">
                                        <p class="text-[10px] text-charcoal-400 uppercase font-bold tracking-widest mb-1">Parking</p>
                                        <p class="text-sm font-bold text-charcoal-800">Street Parking Available</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Edit Mode -->
                    <div x-show="editingVenue" x-cloak class="p-8">
                        <div class="flex items-center justify-between mb-6">
                            <h2 class="serif-heading text-2xl font-bold text-charcoal-900">Edit Venue & Location</h2>
                        </div>
                        
                        <div class="space-y-6">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-xs font-bold text-charcoal-500 mb-1 uppercase">Location Name</label>
                                    <input type="text" x-model="venueData.location_name" @change="geocodeLocation()" class="w-full px-4 py-3 rounded-xl border-2 border-gray-200 focus:border-brand-500 outline-none transition bg-gray-50 focus:bg-white">
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-charcoal-500 mb-1 uppercase">City / Address</label>
                                    <input type="text" x-model="venueData.city" @change="geocodeLocation()" class="w-full px-4 py-3 rounded-xl border-2 border-gray-200 focus:border-brand-500 outline-none transition bg-gray-50 focus:bg-white">
                                </div>
                            </div>
                            
                            <div>
                                 <label class="block text-xs font-bold text-charcoal-500 mb-2 uppercase flex justify-between">
                                    <span>Pin Location on Map</span>
                                    <span class="text-brand-600 normal-case font-normal">Map auto-updates or drag marker</span>
                                 </label>
                                 <div id="venueEditMap" class="h-80 w-full rounded-2xl border-2 border-charcoal-200 overflow-hidden shadow-inner"></div>
                            </div>

                            <div class="flex justify-end gap-3 pt-6 border-t border-gray-100">
                                <button @click="editingVenue = false" class="px-6 py-3 bg-gray-100 text-gray-700 font-bold rounded-xl hover:bg-gray-200 transition">Cancel</button>
                                <button @click="saveVenue()" 
                                        :disabled="savingVenue"
                                        class="px-8 py-3 bg-brand-600 text-white font-bold rounded-xl hover:bg-brand-700 transition shadow-lg shadow-brand-500/30 disabled:opacity-50 flex items-center gap-2">
                                    <span x-show="!savingVenue">Save Changes</span>
                                    <span x-show="savingVenue">Saving...</span>
                                </button>
                            </div>
                        </div>
                    </div>

                </div>
            </div>

            <!-- FAQ Tab -->
            <div x-show="selectedTab === 'faq'" x-transition>
                <div class="bg-white rounded-3xl p-8 border-2 border-charcoal-200 shadow-sm relative">
                
                    <?php if($isOrganizer): ?>
                    <div class="absolute top-8 right-8 z-10">
                         <button @click="editingFaqs = true" 
                                 x-show="!editingFaqs"
                                 class="p-2 hover:bg-brand-50 rounded-lg transition group"
                                 title="Edit FAQs">
                             <i data-lucide="edit-2" class="w-5 h-5 text-brand-600 group-hover:scale-110 transition-transform"></i>
                         </button>
                    </div>
                    <?php endif; ?>

                    <h2 x-show="!editingFaqs" class="serif-heading text-3xl font-bold text-charcoal-900 mb-8">Frequently Asked Questions</h2>

                    <!-- View Mode -->
                    <div x-show="!editingFaqs" class="space-y-4">
                        <?php if(empty($faqs)): ?>
                             <div class="text-center py-10 opacity-50">
                                <i data-lucide="help-circle" class="w-12 h-12 mx-auto mb-2 text-charcoal-300"></i>
                                <p class="text-charcoal-500 text-sm">No FAQs added yet.</p>
                             </div>
                        <?php else: ?>
                            <!-- Alpine Accordion -->
                            <div x-data="{ active: null }" class="space-y-3">
                                <?php foreach($faqs as $index => $faq): ?>
                                <div class="border-2 border-charcoal-100 rounded-2xl overflow-hidden transition hover:border-brand-200 bg-white">
                                    <button @click="active === <?php echo $index; ?> ? active = null : active = <?php echo $index; ?>" 
                                            class="w-full flex items-center justify-between p-5 text-left font-bold text-charcoal-800 bg-cream-50 hover:bg-brand-50 transition">
                                        <span><?php echo htmlspecialchars($faq['question']); ?></span>
                                        <i data-lucide="chevron-down" class="w-5 h-5 text-charcoal-400 transition-transform duration-300" :class="active === <?php echo $index; ?> ? 'rotate-180' : ''"></i>
                                    </button>
                                    <div x-show="active === <?php echo $index; ?>" x-collapse>
                                        <div class="p-5 text-charcoal-600 text-sm leading-relaxed border-t border-charcoal-100 bg-white">
                                            <?php echo nl2br(htmlspecialchars($faq['answer'])); ?>
                                        </div>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- Edit Mode -->
                    <div x-show="editingFaqs" x-cloak>
                        <h2 class="serif-heading text-2xl font-bold text-charcoal-900 mb-6">Edit FAQs</h2>
                        
                        <div class="space-y-4 mb-6">
                            <template x-for="(item, index) in faqItems" :key="index">
                                <div class="p-4 bg-gray-50 rounded-2xl border border-gray-200 relative group">
                                    <div class="space-y-3">
                                        <div>
                                            <label class="block text-xs font-bold text-charcoal-500 mb-1 uppercase">Question</label>
                                            <input type="text" x-model="item.question" class="w-full px-4 py-2 rounded-xl border border-gray-300 focus:border-brand-500 outline-none bg-white">
                                        </div>
                                        <div>
                                            <label class="block text-xs font-bold text-charcoal-500 mb-1 uppercase">Answer</label>
                                            <textarea x-model="item.answer" rows="2" class="w-full px-4 py-2 rounded-xl border border-gray-300 focus:border-brand-500 outline-none bg-white resize-none"></textarea>
                                        </div>
                                    </div>
                                    <button @click="removeFaq(index)" class="absolute top-2 right-2 p-1.5 bg-red-100 text-red-600 rounded-lg opacity-0 group-hover:opacity-100 hover:bg-red-200 transition" title="Remove">
                                        <i data-lucide="trash-2" class="w-4 h-4"></i>
                                    </button>
                                </div>
                            </template>
                        </div>

                        <button @click="addFaq()" class="w-full py-3 border-2 border-dashed border-charcoal-300 rounded-xl text-charcoal-500 font-bold hover:border-brand-500 hover:text-brand-600 transition flex items-center justify-center gap-2 mb-6">
                            <i data-lucide="plus" class="w-5 h-5"></i> Add Question
                        </button>

                        <div class="flex justify-end gap-3 pt-6 border-t border-gray-100">
                            <button @click="editingFaqs = false" class="px-6 py-3 bg-gray-100 text-gray-700 font-bold rounded-xl hover:bg-gray-200 transition">Cancel</button>
                            <button @click="saveFaqs()" 
                                    :disabled="savingFaqs"
                                    class="px-8 py-3 bg-brand-600 text-white font-bold rounded-xl hover:bg-brand-700 transition shadow-lg shadow-brand-500/30 disabled:opacity-50 flex items-center gap-2">
                                <span x-show="!savingFaqs">Save Changes</span>
                                <span x-show="savingFaqs">Saving...</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Memories Tab -->
            <div x-show="selectedTab === 'memories'" x-transition>
                <div class="bg-white rounded-3xl p-8 border-2 border-charcoal-200 shadow-sm">
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="serif-heading text-3xl font-bold text-charcoal-900">Memories</h2>
                        <span class="bg-brand-100 text-brand-800 text-xs font-bold px-3 py-1 rounded-full uppercase tracking-widest">Gallery</span>
                    </div>
                    <p class="text-charcoal-600 mb-8 italic border-l-4 border-brand-400 pl-4 py-1">
                        "Created memories at event? Please share with us"
                    </p>

                    <!-- Submission Form -->
                    <form action="<?php echo BASE_URL; ?>event/<?php echo $event['id']; ?>/memories" method="POST" class="mb-10 bg-cream-50 p-6 rounded-2xl border-2 border-dashed border-charcoal-300">
                        <div class="space-y-4">
                            <div>
                                <label class="block text-xs font-bold text-charcoal-700 uppercase tracking-wider mb-2">Image URL</label>
                                <div class="flex gap-2">
                                    <div class="bg-white p-3 rounded-xl border-2 border-charcoal-200 flex items-center justify-center text-charcoal-400">
                                        <i data-lucide="link" class="w-5 h-5"></i>
                                    </div>
                                    <input type="url" name="image_url" required placeholder="https://example.com/photo.jpg" 
                                        class="w-full bg-white border-2 border-charcoal-200 rounded-xl px-4 py-3 text-charcoal-900 focus:outline-none focus:border-brand-500 transition">
                                </div>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-charcoal-700 uppercase tracking-wider mb-2">Caption (Optional)</label>
                                <input type="text" name="caption" placeholder="Best moment ever!" 
                                    class="w-full bg-white border-2 border-charcoal-200 rounded-xl px-4 py-3 text-charcoal-900 focus:outline-none focus:border-brand-500 transition">
                            </div>
                            <button type="submit" class="w-full bg-charcoal-900 hover:bg-black text-white font-bold py-3 rounded-xl shadow-md transition transform hover:scale-[1.01] flex items-center justify-center gap-2">
                                <i data-lucide="upload-cloud" class="w-5 h-5"></i> Share Memory
                            </button>
                        </div>
                    </form>

                    <!-- Memories Grid -->
                    <?php if (!empty($memories)): ?>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <?php foreach($memories as $memory): ?>
                                <div class="group relative aspect-square overflow-hidden rounded-2xl bg-gray-100 border-2 border-charcoal-100">
                                    <img src="<?php echo htmlspecialchars($memory['image_url']); ?>" 
                                         class="w-full h-full object-cover transition duration-500 group-hover:scale-110" 
                                         alt="Event memory">
                                    <?php if (!empty($memory['caption'])): ?>
                                        <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex items-end p-4">
                                            <p class="text-white font-medium text-sm"><?php echo htmlspecialchars($memory['caption']); ?></p>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-10 opacity-50">
                            <i data-lucide="image" class="w-12 h-12 mx-auto mb-2 text-charcoal-300"></i>
                            <p class="text-charcoal-500 text-sm">No memories shared yet. Be the first!</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Community Tab -->
            <div x-show="selectedTab === 'community'" x-transition>
                <div class="bg-white rounded-3xl border-2 border-charcoal-200 overflow-hidden shadow-sm">
                    <div class="p-4 border-b-2 border-charcoal-100 flex items-center justify-between bg-cream-50">
                        <h2 class="text-xl font-bold text-charcoal-900 flex items-center gap-2">
                            <i data-lucide="users" class="text-brand-500"></i> Community
                        </h2>
                        <div class="flex bg-white rounded-xl p-1 border-2 border-charcoal-100">
                            <button @click="communityTab = 'chat'" 
                                :class="communityTab === 'chat' ? 'bg-brand-500 text-white shadow-md' : 'text-charcoal-500 hover:bg-gray-100'"
                                class="px-4 py-1.5 rounded-lg text-xs font-bold transition flex items-center gap-2">
                                <i data-lucide="message-square" class="w-3 h-3"></i> Chat
                            </button>
                            <button @click="communityTab = 'polls'; fetchPolls()" 
                                :class="communityTab === 'polls' ? 'bg-brand-500 text-white shadow-md' : 'text-charcoal-500 hover:bg-gray-100'"
                                class="px-4 py-1.5 rounded-lg text-xs font-bold transition flex items-center gap-2">
                                <i data-lucide="bar-chart-2" class="w-3 h-3"></i> Polls
                            </button>
                        </div>
                    </div>

                    <!-- Chat View -->
                    <div x-show="communityTab === 'chat'" x-transition:enter>
                        <div class="h-96 overflow-y-auto p-6 space-y-4 custom-scrollbar bg-white" id="chat-window">
                            <template x-for="msg in messages">
                                <div class="flex gap-4" :class="msg.user === '<?= $_SESSION['user_name'] ?? '' ?>' ? 'flex-row-reverse' : 'flex-row'">
                                    <div class="flex flex-col" :class="msg.user === '<?= $_SESSION['user_name'] ?? '' ?>' ? 'items-end' : 'items-start'">
                                        <div class="px-4 py-2 rounded-2xl max-w-[90%] shadow-sm" 
                                            :class="msg.user === '<?= $_SESSION['user_name'] ?? '' ?>' ? 'bg-brand-500 text-white rounded-tr-none' : 'bg-gray-100 text-charcoal-800 rounded-tl-none border-2 border-gray-100'">
                                            <p class="text-sm" x-text="msg.content"></p>
                                        </div>
                                        <span class="text-[9px] text-charcoal-400 mt-1 uppercase font-bold tracking-widest" x-text="msg.user + ' • ' + msg.time"></span>
                                    </div>
                                </div>
                            </template>
                        </div>
                        <div class="p-4 bg-gray-50 border-t-2 border-charcoal-100">
                            <div class="flex gap-2">
                                <input type="text" x-model="newMessage" @keyup.enter="sendMessage()" placeholder="Say something..." 
                                    class="flex-grow bg-white border-2 border-charcoal-200 rounded-xl px-4 py-3 text-charcoal-900 text-sm focus:outline-none focus:border-brand-500 transition">
                                <button @click="sendMessage()" class="bg-brand-500 text-white p-3 rounded-xl hover:bg-brand-600 transition shadow-lg shadow-brand-500/20">
                                    <i data-lucide="send" class="w-5 h-5"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Polls View -->
                    <div x-show="communityTab === 'polls'" x-transition:enter class="h-[460px] overflow-y-auto p-6 custom-scrollbar bg-white">
                        
                        <?php if($isOrganizer): ?>
                        <div class="mb-8 bg-cream-50 p-6 rounded-2xl border-2 border-dashed border-charcoal-200">
                            <h3 class="font-bold text-charcoal-900 mb-4 text-sm uppercase tracking-wide">Create New Poll</h3>
                            <div class="space-y-3">
                                <input type="text" x-model="newPoll.question" placeholder="Ask a question..." class="w-full bg-white border-2 border-charcoal-200 rounded-xl px-4 py-2 text-sm focus:outline-none focus:border-brand-500">
                                <template x-for="(opt, index) in newPoll.options">
                                    <div class="flex gap-2">
                                        <input type="text" x-model="newPoll.options[index]" :placeholder="'Option ' + (index + 1)" class="w-full bg-white border-2 border-charcoal-200 rounded-xl px-4 py-2 text-sm focus:outline-none focus:border-brand-500">
                                    </div>
                                </template>
                                <button @click="createPoll()" class="w-full bg-charcoal-900 text-white font-bold py-2 rounded-xl text-sm hover:bg-black transition">Post Poll</button>
                            </div>
                        </div>
                        <?php endif; ?>

                        <div class="space-y-6">
                            <template x-for="poll in polls">
                                <div class="border-2 border-charcoal-100 rounded-2xl p-6 bg-white hover:border-brand-200 transition">
                                    <h3 class="font-bold text-xl text-charcoal-900 mb-4" x-text="poll.question"></h3>
                                    
                                    <div class="space-y-3">
                                        <template x-for="option in poll.options">
                                            <div class="relative">
                                                <!-- Voting Button / Result Bar -->
                                                <button @click="votePoll(poll.id, option.id)" 
                                                    :disabled="poll.user_voted_option"
                                                    class="w-full relative overflow-hidden rounded-xl border-2 transition h-12 flex items-center px-4"
                                                    :class="poll.user_voted_option === option.id ? 'border-brand-500 bg-brand-50' : 'border-charcoal-100 hover:border-charcoal-300 bg-white'">
                                                    
                                                    <!-- Progress Bar Background -->
                                                    <div class="absolute left-0 top-0 bottom-0 bg-brand-100 transition-all duration-500"
                                                         :style="'width: ' + (poll.total_votes > 0 ? (option.votes / poll.total_votes * 100) : 0) + '%'"></div>
                                                    
                                                    <!-- Content -->
                                                    <div class="relative z-10 flex justify-between w-full">
                                                        <span class="font-bold text-sm text-charcoal-800" x-text="option.option_text"></span>
                                                        <span class="font-bold text-xs text-brand-600" x-show="poll.total_votes > 0" x-text="Math.round(poll.total_votes > 0 ? (option.votes / poll.total_votes * 100) : 0) + '%'"></span>
                                                    </div>
                                                </button>
                                            </div>
                                        </template>
                                    </div>
                                    <p class="text-[10px] text-charcoal-400 font-bold uppercase tracking-widest mt-4 text-right" x-text="poll.total_votes + ' votes'"></p>
                                </div>
                            </template>
                            
                            <div x-show="polls.length === 0" class="text-center py-12 opacity-50">
                                <i data-lucide="bar-chart-2" class="w-12 h-12 mx-auto mb-3 text-charcoal-300"></i>
                                <p class="text-charcoal-500 text-sm">No polls active.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Refer Tab -->
            <div x-show="selectedTab === 'refer'" x-transition>
                <div class="bg-white rounded-3xl p-8 border-2 border-charcoal-200 shadow-sm relative overflow-hidden">
                    <div class="absolute -right-10 -top-10 w-40 h-40 bg-brand-50 rounded-full opacity-50"></div>
                    <div class="relative">
                        <div class="flex items-center gap-4 mb-6">
                            <div class="w-12 h-12 bg-brand-100 rounded-2xl flex items-center justify-center text-brand-600">
                                <i data-lucide="user-plus" class="w-6 h-6"></i>
                            </div>
                            <div>
                                <h2 class="serif-heading text-3xl font-bold text-charcoal-900">Refer a Friend</h2>
                                <p class="text-charcoal-500 text-sm font-medium">Loved this event? Share this exclusive experience with someone special.</p>
                            </div>
                        </div>

                        <div class="bg-cream-50 p-6 md:p-8 rounded-3xl border-2 border-charcoal-200 max-w-2xl mx-auto shadow-inner">
                            <h3 class="font-bold text-charcoal-800 mb-6 text-center text-lg">Invite your friend to join this event</h3>
                            <div class="space-y-4 mb-6">
                                <div>
                                    <label class="block text-xs font-bold text-charcoal-700 uppercase tracking-wider mb-2">Friend's Name</label>
                                    <input type="text" x-model="referral.name" placeholder="John Doe" 
                                        class="w-full bg-white border-2 border-charcoal-100 rounded-xl px-4 py-3 text-charcoal-900 focus:outline-none focus:border-brand-500 transition shadow-sm">
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-charcoal-700 uppercase tracking-wider mb-2">Friend's Email</label>
                                    <input type="email" x-model="referral.email" placeholder="john@example.com" 
                                        class="w-full bg-white border-2 border-charcoal-100 rounded-xl px-4 py-3 text-charcoal-900 focus:outline-none focus:border-brand-500 transition shadow-sm">
                                </div>
                            </div>
                            <button @click="submitReferral()" :disabled="isReferring"
                                class="w-full bg-charcoal-900 hover:bg-black text-white font-bold py-4 rounded-xl shadow-lg transition transform hover:scale-[1.01] flex items-center justify-center gap-3">
                                <span x-show="!isReferring">Send Formal Invitation</span>
                                <span x-show="isReferring">Sending Invitation...</span>
                                <i data-lucide="send" x-show="!isReferring" class="w-5 h-5"></i>
                            </button>

                            <div x-show="referralSuccess" x-transition class="mt-4 p-4 bg-green-50 border-2 border-green-200 rounded-xl flex items-center gap-3 text-green-700 shadow-sm">
                                <i data-lucide="check-circle" class="w-5 h-5"></i>
                                <span class="font-bold">Formal invitation has been sent to your friend!</span>
                            </div>
                        </div>

                        <div class="mt-12 grid grid-cols-1 sm:grid-cols-3 gap-8">
                            <div class="flex flex-col items-center text-center">
                                <div class="w-12 h-12 bg-cream-100 rounded-full flex items-center justify-center mb-3 border-2 border-charcoal-50">
                                    <i data-lucide="mail" class="w-6 h-6 text-brand-600"></i>
                                </div>
                                <h4 class="font-bold text-charcoal-800 mb-1">Formal Email</h4>
                                <p class="text-xs text-charcoal-500 px-4">We send a beautifully crafted invitation email with all event details.</p>
                            </div>
                            <div class="flex flex-col items-center text-center">
                                <div class="w-12 h-12 bg-cream-100 rounded-full flex items-center justify-center mb-3 border-2 border-charcoal-50">
                                    <i data-lucide="shield-check" class="w-6 h-6 text-brand-600"></i>
                                </div>
                                <h4 class="font-bold text-charcoal-800 mb-1">Verified Reference</h4>
                                <p class="text-xs text-charcoal-500 px-4">Your name is included as a trusted referrer for priority approval.</p>
                            </div>
                            <div class="flex flex-col items-center text-center">
                                <div class="w-12 h-12 bg-cream-100 rounded-full flex items-center justify-center mb-3 border-2 border-charcoal-50">
                                    <i data-lucide="sparkles" class="w-6 h-6 text-brand-600"></i>
                                </div>
                                <h4 class="font-bold text-charcoal-800 mb-1">Join the Circle</h4>
                                <p class="text-xs text-charcoal-500 px-4">Grow our curated community with like-minded individuals.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Column: Sidebar -->
        <div class="space-y-6">
            <div class="bg-white rounded-3xl p-8 border-2 border-brand-400 shadow-lg sticky top-24">
                <div class="mb-6">
                    <p class="text-brand-600 font-bold uppercase tracking-widest text-xs mb-1">Date & Time</p>
                    <p class="text-charcoal-900 text-lg font-semibold"><?php echo date('l, M j, Y', strtotime($event['start_time'])); ?></p>
                    <p class="text-charcoal-600 text-sm"><?php echo date('g:i A', strtotime($event['start_time'])); ?></p>
                </div>

                <hr class="border-charcoal-200 mb-6">

                <!-- Dynamic Tiers -->
                <div class="space-y-4 mb-8">
                    <p class="text-charcoal-600 font-bold uppercase tracking-widest text-xs">Ticket Options</p>
                    <?php if (!empty($ticketTiers)): ?>
                        <?php foreach($ticketTiers as $index => $tier): ?>
                            <div @click="selectedTier = <?php echo $tier['id']; ?>" class="p-4 rounded-2xl border-2 transition cursor-pointer <?php echo $index === 0 ? 'bg-brand-50 border-brand-400 active-tier' : 'bg-cream-50 border-charcoal-200 hover:border-brand-400'; ?>" :class="selectedTier == <?php echo $tier['id']; ?> ? 'bg-brand-50 border-brand-400 active-tier' : ''">
                                <div class="flex justify-between items-center mb-1">
                                    <span class="text-charcoal-900 font-bold"><?php echo htmlspecialchars($tier['name']); ?></span>
                                    <span class="text-brand-600 font-bold"><?php echo is_numeric($tier['price']) && $tier['price'] > 0 ? '$' . number_format($tier['price'], 2) : ($tier['price'] === 'Free' ? 'Free' : $tier['price']); ?></span>
                                </div>
                                <?php if (!empty($tier['quantity_available'])): ?>
                                    <p class="text-xs text-charcoal-500 italic">Limited spots. (<?php echo $tier['quantity_available']; ?> left)</p>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <!-- Fallback if no specific tiers defined -->
                        <div class="p-4 bg-brand-50 rounded-2xl border-2 border-brand-400 cursor-pointer hover:border-brand-500 transition active-tier">
                            <div class="flex justify-between items-center mb-1">
                                <span class="text-charcoal-900 font-bold">General Admission</span>
                                <span class="text-brand-600 font-bold">Free</span>
                            </div>
                            <p class="text-xs text-charcoal-500 italic">Standard access. Requires host approval.</p>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- RSVP Form/Button -->
                <div x-show="rsvpStatus === null">
                    <div class="mb-4 space-y-4">
                        <!-- Smart Contact Collection -->
                        <div x-show="!hasName">
                            <label class="block text-xs font-semibold text-charcoal-700 mb-2 uppercase tracking-wider">Your Full Name</label>
                            <input type="text" x-model="name" class="w-full bg-cream-50 border-2 border-charcoal-200 rounded-xl px-4 py-2 text-charcoal-900 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-brand-500" placeholder="Enter your full name">
                        </div>
                        <div x-show="!hasEmail">
                            <label class="block text-xs font-semibold text-charcoal-700 mb-2 uppercase tracking-wider">Your Email</label>
                            <input type="email" x-model="email" class="w-full bg-cream-50 border-2 border-charcoal-200 rounded-xl px-4 py-2 text-charcoal-900 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-brand-500" placeholder="Enter your email">
                        </div>
                        
                        <!-- Logged In As Indicator -->
                        <div x-show="hasName" class="flex items-center gap-2 text-sm text-charcoal-500 bg-charcoal-50 px-3 py-2 rounded-lg">
                            <i data-lucide="user" class="w-4 h-4"></i>
                            <span>Joining as <span class="font-bold text-charcoal-900" x-text="name"></span></span>
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-charcoal-700 mb-2 uppercase tracking-wider">Why do you want to join?</label>
                            <textarea x-model="interest" rows="2" class="w-full bg-cream-50 border-2 border-charcoal-200 rounded-xl px-4 py-2 text-charcoal-900 text-sm focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-brand-500" placeholder="I love tech and community..."></textarea>
                        </div>
                    </div>
                    
                    <!-- PayPal Container -->
                    <div id="paypal-button-container" x-show="showPayPal" class="mb-4"></div>

                    <button @click="submitRSVP()" 
                        x-show="!showPayPal"
                        :disabled="isSubmitting"
                        class="w-full bg-brand-500 hover:bg-brand-600 text-white font-bold py-4 rounded-2xl shadow-lg shadow-brand-500/30 transition transform hover:scale-[1.02] mb-4 disabled:opacity-50 disabled:cursor-not-allowed">
                        <span x-show="!isSubmitting">Request to Join</span>
                        <span x-show="isSubmitting">Sending...</span>
                    </button>
                    <p class="text-center text-[10px] text-charcoal-500 px-4">
                        By requesting, you agree to the host's rules. This event requires <span x-text="requiresApproval ? '**manual approval**' : '**no approval**'"></span> by the organizer.
                    </p>
                </div>

                <div x-show="rsvpStatus === 'pending'" class="text-center py-6 bg-yellow-50 rounded-2xl border-2 border-yellow-400">
                    <i data-lucide="clock" class="text-yellow-600 w-10 h-10 mx-auto mb-3"></i>
                    <h4 class="text-charcoal-900 font-bold mb-1">Request Pending</h4>
                    <p class="text-charcoal-600 text-xs">The organizer will review your request soon.</p>
                </div>

                <div x-show="rsvpStatus === 'approved'" class="space-y-4">
                    <div class="text-center py-6 bg-green-50 rounded-2xl border-2 border-green-400">
                        <i data-lucide="check-circle" class="text-green-600 w-10 h-10 mx-auto mb-3"></i>
                        <h4 class="text-charcoal-900 font-bold mb-1">You're In!</h4>
                        <p class="text-charcoal-600 text-xs">Your request has been approved by the host.</p>
                    </div>
                    <a href="<?php echo BASE_URL; ?>event/<?php echo $event['id']; ?>/download-kit" 
                       class="w-full bg-brand-600 text-white hover:bg-brand-700 font-bold py-4 rounded-2xl shadow-lg transition transform hover:scale-[1.02] flex items-center justify-center gap-2">
                        <i data-lucide="download" class="w-5 h-5"></i>
                        Download Event Kit
                    </a>
                </div>

                <div x-show="rsvpStatus === 'rejected'" class="text-center py-6 bg-red-50 rounded-2xl border-2 border-red-400">
                    <i data-lucide="x-circle" class="text-red-600 w-10 h-10 mx-auto mb-3"></i>
                    <h4 class="text-charcoal-900 font-bold mb-1">Request Declined</h4>
                    <p class="text-charcoal-600 text-xs">Sorry, the host could not approve your request.</p>
                </div>
            </div>

            <!-- Organizer Info -->
            <div class="bg-white rounded-3xl p-6 border-2 border-charcoal-200 shadow-sm">
                <div class="flex items-center gap-4">
                    <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($event['organizer_name'] ?? 'Organizer'); ?>&background=random" class="w-12 h-12 rounded-full border-2 border-charcoal-200">
                    <div>
                        <p class="text-xs text-charcoal-500 mb-0.5">Hosted by</p>
                        <p class="text-charcoal-900 font-bold"><?php echo htmlspecialchars($event['organizer_name'] ?? 'Event Organizer'); ?></p>
                    </div>
                </div>
            </div>
        </div>

    <!-- Seating Modal -->
    <div x-show="showSeating" class="fixed inset-0 z-[60] flex items-center justify-center p-4 bg-black/80 backdrop-blur-sm" style="display: none;">
        <div class="bg-white dark:bg-charcoal-900 w-full max-w-4xl rounded-3xl border-2 border-charcoal-200 dark:border-charcoal-700 shadow-2xl overflow-hidden flex flex-col max-h-[90vh]">
            <div class="p-6 border-b border-charcoal-200 dark:border-charcoal-700 flex justify-between items-center bg-cream-50 dark:bg-charcoal-800">
                <div>
                     <h2 class="text-2xl font-bold text-charcoal-900 dark:text-white">Select Your Seat</h2>
                     <p class="text-charcoal-500 dark:text-charcoal-400 text-sm">Frontend is facing screen</p>
                </div>
                <button @click="showSeating = false" class="text-charcoal-400 hover:text-charcoal-900 dark:hover:text-white transition">
                     <i data-lucide="x" class="w-6 h-6"></i>
                </button>
            </div>
            
            <!-- Stage -->
            <div class="p-4 bg-charcoal-100 dark:bg-charcoal-900 flex justify-center">
                 <div class="w-3/4 h-8 bg-charcoal-300 dark:bg-charcoal-700 rounded-b-xl flex items-center justify-center text-charcoal-500 dark:text-charcoal-400 font-bold uppercase tracking-[0.5em] text-xs shadow-inner">
                     Stage / Screen
                 </div>
            </div>

            <!-- Grid Container -->
            <div class="flex-1 overflow-auto p-8 bg-white dark:bg-charcoal-900 flex justify-center items-start custom-scrollbar">
                <div x-show="!seatsLoaded" class="flex flex-col items-center justify-center h-full">
                     <i data-lucide="loader" class="animate-spin w-8 h-8 text-brand-500 mb-2"></i>
                     <p class="text-charcoal-500">Loading seating chart...</p>
                </div>

                <div x-show="seatsLoaded" class="grid gap-2" :style="`grid-template-columns: repeat(${layout.cols}, minmax(40px, 1fr))`">
                     <template x-for="seat in seats" :key="seat.id">
                          <button @click="selectSeat(seat)" 
                             :disabled="seat.status !== 'available'"
                             class="w-10 h-10 rounded-lg flex items-center justify-center text-xs font-bold transition transform shadow-sm relative group"
                             :class="{
                                 'bg-charcoal-200 text-charcoal-400 cursor-not-allowed': seat.status !== 'available',
                                 'bg-cream-100 border-2 border-charcoal-200 hover:border-brand-400 text-charcoal-700 hover:bg-brand-50': seat.status === 'available' && selectedSeatId !== seat.id && seat.section !== 'VIP',
                                 'bg-purple-100 border-2 border-purple-300 hover:border-purple-500 text-purple-800 hover:bg-purple-200': seat.status === 'available' && selectedSeatId !== seat.id && seat.section === 'VIP',
                                 'bg-brand-500 text-white border-2 border-brand-600 ring-2 ring-brand-200 scale-110 z-10': selectedSeatId === seat.id
                             }">
                             <span x-text="seat.col_label"></span>
                             <!-- Tooltip -->
                             <div class="absolute bottom-full mb-2 left-1/2 -translate-x-1/2 bg-charcoal-900 text-white text-[10px] px-2 py-1 rounded shadow-lg opacity-0 group-hover:opacity-100 pointer-events-none whitespace-nowrap z-20">
                                 <span x-text="seat.row_label + seat.col_label"></span> • <span x-text="'$' + seat.tier_price"></span>
                                 <span x-text="seat.section === 'VIP' ? '(VIP)' : ''" class="text-yellow-400 ml-1"></span>
                             </div>
                          </button>
                     </template>
                </div>
            </div>

            <!-- Footer -->
            <div class="p-6 border-t border-charcoal-200 dark:border-charcoal-700 bg-cream-50 dark:bg-charcoal-800 flex justify-between items-center">
                 <div>
                      <p class="text-xs font-bold text-charcoal-500 uppercase tracking-wider mb-1">Selected Seat</p>
                      <h4 class="text-xl font-bold text-charcoal-900 dark:text-white transition-all" x-text="selectedSeat ? (selectedSeat.row_label + selectedSeat.col_label + ' (' + selectedSeat.section + ')') : 'None'"></h4>
                      <p class="text-brand-600 font-bold" x-show="selectedSeat" x-text="'Price: $' + selectedSeat.tier_price">-</p>
                 </div>
                 <button @click="confirmSeat()" :disabled="!selectedSeat" class="px-8 py-3 bg-brand-500 text-white font-bold rounded-xl shadow-lg hover:bg-brand-600 disabled:opacity-50 disabled:cursor-not-allowed transition">
                      Confirm Selection
                 </button>
            </div>
        </div>
    </div>
    </div>
</div>

<!-- Leaflet Map Dependencies -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<script src="https://www.paypal.com/sdk/js?client-id=sb&currency=USD"></script>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

<script>
let mapInitialized = false;
function initMap() {
    if (mapInitialized) return;
    
    // Server-side values
    const dbLat = <?php echo !empty($event['latitude']) ? $event['latitude'] : 0; ?>;
    const dbLng = <?php echo !empty($event['longitude']) ? $event['longitude'] : 0; ?>;
    const locName = <?php echo json_encode($event['location_name'] ?? ''); ?>;
    const locCity = <?php echo json_encode($event['city'] ?? ''); ?>;

    // Use DB values or Default (Bengaluru)
    let lat = dbLat || 12.9716;
    let lng = dbLng || 77.5946;
    
    const map = L.map('venueMap', {
        zoomControl: false,
        scrollWheelZoom: false,
        dragging: true,
        touchZoom: true
    }).setView([lat, lng], 15);

    // Use Standard OSM for better visibility
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '© OpenStreetMap contributors'
    }).addTo(map);

    const customIcon = L.divIcon({
        html: `<div class="w-10 h-10 bg-brand-500 rounded-full border-4 border-white shadow-xl flex items-center justify-center">
                <div class="w-3 h-3 bg-white rounded-full"></div>
               </div>`,
        className: '',
        iconSize: [40, 40],
        iconAnchor: [20, 20]
    });

    const marker = L.marker([lat, lng], {icon: customIcon}).addTo(map);
    
    // Auto-correct map if coordinates are missing/default but location name is specific
    // This fixes the issue where map stays at default location despite specific venue name
    if ((!dbLat || (Math.abs(dbLat - 12.9716) < 0.01 && !locName.toLowerCase().includes('bengaluru'))) && locName) {
        const query = [locName, locCity].filter(Boolean).join(', ');
        fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(query)}`)
            .then(res => res.json())
            .then(data => {
                if(data && data.length > 0) {
                    const newLat = parseFloat(data[0].lat);
                    const newLng = parseFloat(data[0].lon);
                    map.setView([newLat, newLng], 15);
                    marker.setLatLng([newLat, newLng]);
                }
            })
            .catch(e => console.error('Auto-map update failed', e));
    }

    // Invalidate size in case container was hidden
    setTimeout(() => map.invalidateSize(), 200);
    mapInitialized = true;
}

function countdown(eventDate) {
    return {
        days: '00',
        hours: '00',
        minutes: '00',
        seconds: '00',
        
        init() {
            this.updateCountdown();
            setInterval(() => this.updateCountdown(), 1000);
        },
        
        updateCountdown() {
            const now = new Date().getTime();
            const eventTime = new Date(eventDate).getTime();
            const distance = eventTime - now;
            
            if (distance < 0) {
                this.days = '00';
                this.hours = '00';
                this.minutes = '00';
                this.seconds = '00';
                return;
            }
            
            this.days = String(Math.floor(distance / (1000 * 60 * 60 * 24))).padStart(2, '0');
            this.hours = String(Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60))).padStart(2, '0');
            this.minutes = String(Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60))).padStart(2, '0');
            this.seconds = String(Math.floor((distance % (1000 * 60)) / 1000)).padStart(2, '0');
        }
    }
}

function eventPage() {
    return {
        selectedTab: 'about',
        communityTab: 'chat',
        messages: [],
        newMessage: '',
        rsvpStatus: <?php echo json_encode($rsvpStatus); ?>,
        interest: '',
        selectedTier: <?php echo !empty($ticketTiers) ? $ticketTiers[0]['id'] : 'null'; ?>,
        isSubmitting: false,
        showPayPal: false,
        showSuccessModal: false,
        isReferring: false,
        referralSuccess: false,
        referral: {
            name: '',
            email: ''
        },
        name: '<?php echo $currentUser['name'] ?? ''; ?>', 
        email: '<?php echo $currentUser['email'] ?? ''; ?>',
        hasName: <?php echo !empty($currentUser['name']) ? 'true' : 'false'; ?>,
        hasEmail: <?php echo !empty($currentUser['email']) ? 'true' : 'false'; ?>,
        requiresApproval: <?php echo ($event['requires_approval'] ?? 1) == 1 ? 'true' : 'false'; ?>,
        newPoll: { question: '', options: ['', ''] },
        eventId: <?= $event['id'] ?>,
        chatInterval: null,
        hasSeating: <?= !empty($event['has_seating']) ? 'true' : 'false' ?>, 
        showSeating: false,
        seats: [],
        layout: { cols: 10 }, // default
        seatsLoaded: false,
        selectedSeatId: null,
        selectedSeat: null,
        
        init() {
            this.fetchPolls();
            
            this.$watch('communityTab', value => { if (value === 'chat') { this.fetchMessages(); } });
            this.$watch('activeTab', value => { if (value === 'community') { this.fetchMessages(); } });
            if (this.activeTab === 'community') { this.fetchMessages(); }
            
            lucide.createIcons();
        },

        // Seating Methods
        openSeating() {
             if (!this.seatsLoaded) {
                 this.loadSeats();
             }
             this.showSeating = true;
        },

        async loadSeats() {
             try {
                 const res = await fetch(`<?php echo BASE_URL; ?>event/${this.eventId}/seats-json`);
                 const data = await res.json();
                 this.seats = data.seats;
                 // Calculate grid size
                 if(this.seats.length > 0) {
                      const maxCol = Math.max(...this.seats.map(s => parseInt(s.col_label)));
                      this.layout.cols = maxCol;
                 }
                 this.seatsLoaded = true;
             } catch(e) {
                 console.error("Failed to load seats", e);
             }
        },

        selectSeat(seat) {
             this.selectedSeatId = seat.id;
             this.selectedSeat = seat;
        },

        confirmSeat() {
             if(this.selectedSeat) {
                 this.showSeating = false;
                 // Proceed to RSVP submission now
                 this.finalSubmitRSVP();
             }
        },

        // ... methods ...

        async submitRSVP() {
            // Validate: If name/email not set (and we don't have them), alert
            if(!this.interest) {
                 alert('Please tell the host why you want to join.');
                 return;
            }
            if (!this.hasName && !this.name) {
                alert('Please provide your name.');
                return;
            }
            if (!this.hasEmail && !this.email) {
                alert('Please provide your email.');
                return;
            }
            
            // Check Seating
            if (this.hasSeating && !this.selectedSeatId) {
                // Open Map instead of submitting
                this.openSeating();
                return;
            }

            this.finalSubmitRSVP();
        },

        async finalSubmitRSVP() {
            this.isSubmitting = true;
            
            try {
                const formData = new FormData();
                formData.append('event_id', <?php echo $event['id']; ?>);
                formData.append('interest', this.interest);
                if (this.selectedTier) formData.append('ticket_tier_id', this.selectedTier);
                
                // Only send if we needed to collect it
                if (!this.hasEmail) formData.append('email', this.email);
                
                // Add Seat
                if (this.selectedSeatId) formData.append('seat_id', this.selectedSeatId);
                
                const response = await fetch('<?php echo BASE_URL; ?>rsvp/submit', {
                    method: 'POST',
                    body: formData
                });
                
                // Check for redirect (Fetch follows redirects automatically)
                if (response.redirected && response.url.includes('/rsvp/success')) {
                    window.location.href = response.url;
                    return;
                }
                
                const data = await response.json();
                
                if (data.status === 'payment_required') {
                     this.showPayPal = true;
                     this.renderPayPal(data.amount, data.rsvp_id);
                     this.isSubmitting = false; // Allow interaction with PayPal
                } else if (data.status === 'success') {
                    // Trigger success modal
                    this.showSuccessModal = true;
                    // Update status in background (modal will handle the rest)
                    this.rsvpStatus = 'pending';
                } else if (data.redirect) {
                    window.location.href = data.redirect;
                } else {
                    alert(data.message || 'Something went wrong');
                    if (data.status === 'error' && data.message === 'Already requested') {
                         this.rsvpStatus = 'pending';
                    }
                }
            } catch (error) {
                console.error('Error:', error);
                
                // If payment required but JSON parse failed (unlikely if backend is correct)
                alert('Connection error. Please try again.');
            } finally {
               if(!this.showPayPal) this.isSubmitting = false;
            }
        },

        renderPayPal(amount, rsvpId) {
            paypal.Buttons({
                createOrder: function(data, actions) {
                    return actions.order.create({
                        purchase_units: [{
                            amount: { value: amount }
                        }]
                    });
                },
                onApprove: function(data, actions) {
                    return actions.order.capture().then(function(details) {
                        // Call backend to verify
                        const formData = new FormData();
                        formData.append('rsvp_id', rsvpId);
                        formData.append('order_id', details.id);
                        
                        return fetch('<?php echo BASE_URL; ?>rsvp/verify_payment', {
                            method: 'POST',
                            body: formData
                        }).then(res => res.json()).then(resData => {
                             if(resData.status === 'success') {
                                 window.location.href = '<?php echo BASE_URL; ?>rsvp/success';
                             } else {
                                 alert('Payment verification failed');
                             }
                        });
                    });
                }
            }).render('#paypal-button-container');
        },

        submitReferral() {
            if (this.isReferring) return;
            if (!this.referral.name || !this.referral.email) {
                alert('Please fill in both name and email');
                return;
            }

            this.isReferring = true;
            this.referralSuccess = false;

            const formData = new FormData();
            formData.append('friend_name', this.referral.name);
            formData.append('friend_email', this.referral.email);

            fetch(`<?php echo BASE_URL; ?>event/<?php echo $event['id']; ?>/refer`, {
                method: 'POST',
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                if (data.status === 'success') {
                    this.referralSuccess = true;
                    this.referral.name = '';
                    this.referral.email = '';
                    setTimeout(() => this.referralSuccess = false, 5000);
                } else {
                    alert(data.message || 'Failed to send referral');
                }
            })
            .catch(err => {
                console.error(err);
                alert('Connection error');
            })
            .finally(() => {
                this.isReferring = false;
            });
        },

        fetchMessages() {
            if(this.activeTab !== 'community') return;
            
            fetch('<?= BASE_URL ?>event/' + this.eventId + '/chat')
                .then(r => r.json())
                .then(data => {
                    if(data.status === 'success') {
                        this.messages = data.messages;
                        // Scroll to bottom if new messages? 
                        this.$nextTick(() => {
                            const container = document.getElementById('chat-container');
                            if(container && (container.scrollTop + container.clientHeight >= container.scrollHeight - 50)) { 
                                // Only scroll if user was already near bottom
                                container.scrollTop = container.scrollHeight;
                            } else if (container && this.messages.length > 0 && container.scrollTop === 0) {
                                // Initial load
                                container.scrollTop = container.scrollHeight;
                            }
                        });
                    }
                })
                .catch(e => console.error(e));
        },
        
        startChatPolling() {
             if (this.chatInterval) clearInterval(this.chatInterval);
             this.fetchMessages(); // Initial fetch
             this.chatInterval = setInterval(() => {
                 this.fetchMessages();
             }, 3000);
        },

        sendMessage() {
            if(!this.newMessage.trim()) return;
            
            let fd = new FormData();
            fd.append('message', this.newMessage);
            
            fetch('<?= BASE_URL ?>event/' + this.eventId + '/send-message', {
                method: 'POST',
                body: fd
            })
            .then(r => r.json())
            .then(data => {
                if(data.status === 'success') {
                     this.messages.push(data.message); // Optimistic update / server confirmation
                     this.newMessage = '';
                     this.$nextTick(() => {
                         const container = document.getElementById('chat-container');
                         if(container) container.scrollTop = container.scrollHeight;
                     });
                } else {
                    alert(data.message || 'Failed to send');
                }
            })
            .catch(e => console.error(e));
        },

        fetchPolls() {
            fetch(`<?php echo BASE_URL; ?>event/<?php echo $event['id']; ?>/polls`)
                .then(res => res.json())
                .then(data => {
                    if (data.status === 'success') {
                         this.polls = data.polls;
                    }
                });
        },

        createPoll() {
            // ... (keep existing poll logic if needed, or if I'm replacing it, include it)
            console.log('Creating poll...', this.newPoll);
            if (!this.newPoll.question || this.newPoll.options.filter(o => o.trim()).length < 2) {
                alert('Please enter a question and at least 2 options.');
                return;
            }

            const formData = new FormData();
            formData.append('question', this.newPoll.question);
            this.newPoll.options.forEach(opt => formData.append('options[]', opt));

            fetch(`<?php echo BASE_URL; ?>event/<?php echo $event['id']; ?>/create-poll`, {
                method: 'POST',
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                if (data.status === 'success') {
                    this.newPoll = { question: '', options: ['', ''] };
                    this.fetchPolls();
                    alert('Poll created successfully!');
                } else {
                    alert(data.message || 'Failed to create poll');
                }
            });
        },

        votePoll(pollId, optionId) {
             const formData = new FormData();
             formData.append('poll_id', pollId);
             formData.append('option_id', optionId);

             fetch(`<?php echo BASE_URL; ?>event/<?php echo $event['id']; ?>/vote-poll`, {
                 method: 'POST',
                 body: formData
             })
             .then(res => res.json())
             .then(data => {
                 if (data.status === 'success') {
                     this.fetchPolls();
                 } else {
                     alert(data.message || 'You have already voted or an error occurred.');
                 }
             });
        },

        // --- Chat Methods ---
        fetchMessages() {
            // Always fetch messages when this function is called
            fetch('<?= BASE_URL ?>event/' + this.eventId + '/chat')
                .then(r => r.json())
                .then(data => {
                    if(data.status === 'success') {
                        this.messages = data.messages;
                        this.$nextTick(() => {
                            // Container ID matches the HTML ID
                            const container = document.getElementById('chat-window');
                            if(container) {
                                // Auto scroll to bottom
                                container.scrollTop = container.scrollHeight;
                            }
                        });
                    }
                })
                .catch(e => console.error(e));
        },

        sendMessage() {
            if(!this.newMessage.trim()) return;
            
            let fd = new FormData();
            fd.append('message', this.newMessage);
            
            fetch('<?= BASE_URL ?>event/' + this.eventId + '/send-message', {
                method: 'POST',
                body: fd
            })
            .then(r => r.json())
            .then(data => {
                if(data.status === 'success') {
                     this.newMessage = '';
                     // Fetch all messages to refresh the list
                     this.fetchMessages();
                } else {
                    alert(data.message || 'Failed to send message');
                }
            })
            .catch(e => {
                console.error(e);
                alert('Connection error');
            });
        },

        // --- About Section Edit/Delete Methods ---
        editingAbout: false,
        aboutDescription: <?php echo json_encode($event['description'] ?? ''); ?>,
        originalDescription: <?php echo json_encode($event['description'] ?? ''); ?>,
        savingAbout: false,

        async saveAboutDescription() {
            this.savingAbout = true;
            
            const formData = new FormData();
            formData.append('description', this.aboutDescription);
            
            try {
                const response = await fetch('<?php echo BASE_URL; ?>event/<?php echo $event['id']; ?>/update-description', {
                    method: 'POST',
                    body: formData
                });
                
                const data = await response.json();
                
                if (data.status === 'success') {
                    this.editingAbout = false;
                    if (typeof window.showToast === 'function') {
                        window.showToast('success', 'Description updated successfully!', 'Success');
                    } else {
                        alert('Description updated successfully!');
                    }
                    // Reload page to show updated description
                    setTimeout(() => window.location.reload(), 1000);
                } else {
                    if (typeof window.showToast === 'function') {
                        window.showToast('error', data.message || 'Failed to update description', 'Error');
                    } else {
                        alert(data.message || 'Failed to update description');
                    }
                }
            } catch (error) {
                console.error('Error updating description:', error);
                if (typeof window.showToast === 'function') {
                    window.showToast('error', 'An error occurred. Please try again.', 'Error');
                } else {
                    alert('An error occurred. Please try again.');
                }
            } finally {
                this.savingAbout = false;
            }
        },

        deleteEvent() {
            if (!confirm('Are you sure you want to delete this event? This action cannot be undone.')) {
                return;
            }
            
            if (!confirm('This will permanently delete all event data, RSVPs, and messages. Are you absolutely sure?')) {
                return;
            }
            
            fetch('<?php echo BASE_URL; ?>event/<?php echo $event['id']; ?>/delete', {
                method: 'POST'
            })
            .then(res => res.json())
            .then(data => {
                if (data.status === 'success') {
                    if (typeof window.showToast === 'function') {
                        window.showToast('success', 'Event deleted successfully', 'Success');
                    } else {
                        alert('Event deleted successfully');
                    }
                    setTimeout(() => {
                        window.location.href = '<?php echo BASE_URL; ?>organizer/dashboard';
                    }, 1500);
                } else {
                    if (typeof window.showToast === 'function') {
                        window.showToast('error', data.message || 'Failed to delete event', 'Error');
                    } else {
                        alert(data.message || 'Failed to delete event');
                    }
                }
            })
            .catch(error => {
                console.error('Error deleting event:', error);
                if (typeof window.showToast === 'function') {
                    window.showToast('error', 'An error occurred. Please try again.', 'Error');
                } else {
                    alert('An error occurred. Please try again.');
                }
            });
        },

        // --- Schedule Edit Methods ---
        editingSchedule: false,
        savingSchedule: false,
        scheduleItems: [], 
        originalSchedule: <?php 
            // Resolve templates to absolute strings for JS edit
            $jsSchedule = [];
            foreach($eventSchedule as $item) {
                if (strpos($item['time'], 'minutes') !== false) {
                     $itemTime = strtotime($item['time'], $eventStartTime);
                     $timeStr = date('g:i A', $itemTime);
                     $jsSchedule[] = ['time' => $timeStr, 'title' => $item['title']];
                } else {
                     $jsSchedule[] = ['time' => $item['time'], 'title' => $item['title']];
                }
            }
            echo json_encode($jsSchedule);
        ?>,
        
        async saveSchedule() {
            this.savingSchedule = true;
            
            // Basic validation
            // Filter out empty items
            this.scheduleItems = this.scheduleItems.filter(item => item.time.trim() !== '' || item.title.trim() !== '');

            const formData = new FormData();
            formData.append('schedule', JSON.stringify(this.scheduleItems));
            
            try {
                const response = await fetch('<?php echo BASE_URL; ?>event/<?php echo $event['id']; ?>/update-schedule', {
                    method: 'POST',
                    body: formData
                });
                
                const data = await response.json();
                
                if (data.status === 'success') {
                    this.editingSchedule = false;
                    if (typeof window.showToast === 'function') {
                        window.showToast('success', 'Schedule updated successfully!', 'Success');
                    } else {
                        alert('Schedule updated successfully!');
                    }
                    setTimeout(() => window.location.reload(), 1000);
                } else {
                    if (typeof window.showToast === 'function') {
                        window.showToast('error', data.message || 'Failed to update schedule', 'Error');
                    } else {
                        alert(data.message || 'Failed to update schedule');
                    }
                }
            } catch (error) {
                console.error('Error updating schedule:', error);
                if (typeof window.showToast === 'function') {
                    window.showToast('error', 'An error occurred. Please try again.', 'Error');
                } else {
                    alert('An error occurred. Please try again.');
                }
            } finally {
                this.savingSchedule = false;
            }
        },

        // --- Venue Edit Methods ---
        editingVenue: false,
        savingVenue: false,
        venueData: {
            location_name: <?php echo json_encode($event['location_name']); ?>,
            city: <?php echo json_encode($event['city']); ?>,
            latitude: <?php echo json_encode($event['latitude']); ?>,
            longitude: <?php echo json_encode($event['longitude']); ?>
        },
        editMap: null,
        editMarker: null,

        initEditMap() {
            if (this.editMap) {
                setTimeout(() => this.editMap.invalidateSize(), 100);
                return;
            }
            
            this.$nextTick(() => {
                const lat = parseFloat(this.venueData.latitude) || 40.7128;
                const lng = parseFloat(this.venueData.longitude) || -74.0060;
                
                // Ensure container exists
                if(!document.getElementById('venueEditMap')) return;

                this.editMap = L.map('venueEditMap').setView([lat, lng], 13);
                
                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    attribution: '© OpenStreetMap contributors'
                }).addTo(this.editMap);
                
                this.editMarker = L.marker([lat, lng], {draggable: true}).addTo(this.editMap);
                
                this.editMarker.on('dragend', (e) => {
                    const pos = e.target.getLatLng();
                    this.venueData.latitude = pos.lat;
                    this.venueData.longitude = pos.lng;
                });
                
                this.editMap.on('click', (e) => {
                     this.editMarker.setLatLng(e.latlng);
                     this.venueData.latitude = e.latlng.lat;
                     this.venueData.longitude = e.latlng.lng;
                });
            });
        },
        
        async saveVenue() {
             this.savingVenue = true;
             const fd = new FormData();
             fd.append('location_name', this.venueData.location_name);
             fd.append('city', this.venueData.city);
             fd.append('latitude', this.venueData.latitude);
             fd.append('longitude', this.venueData.longitude);
             
             try {
                const response = await fetch('<?php echo BASE_URL; ?>event/<?php echo $event['id']; ?>/update-venue', {
                    method: 'POST',
                    body: fd
                });
                
                const data = await response.json();
                
                if (data.status === 'success') {
                    this.editingVenue = false;
                    if (typeof window.showToast === 'function') {
                        window.showToast('success', 'Venue updated successfully!', 'Success');
                    } else {
                        alert('Venue updated successfully!');
                    }
                    setTimeout(() => window.location.reload(), 1000);
                } else {
                    if (typeof window.showToast === 'function') {
                        window.showToast('error', data.message || 'Failed to update venue', 'Error');
                    } else {
                        alert(data.message || 'Failed to update venue');
                    }
                }
            } catch (error) {
                console.error('Error updating venue:', error);
                if (typeof window.showToast === 'function') {
                    window.showToast('error', 'An error occurred. Please try again.', 'Error');
                } else {
                    alert('An error occurred. Please try again.');
                }
            } finally {
                this.savingVenue = false;
            }
        },
        
        async geocodeLocation() {
            const query = [this.venueData.location_name, this.venueData.city].filter(Boolean).join(', ');
            if (!query) return;
            
            try {
                const response = await fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(query)}`);
                const data = await response.json();
                
                if (data && data.length > 0) {
                    const lat = parseFloat(data[0].lat);
                    const lon = parseFloat(data[0].lon);
                    
                    this.venueData.latitude = lat;
                    this.venueData.longitude = lon;
                    
                    if (this.editMap && this.editMarker) {
                         const newLatLng = new L.LatLng(lat, lon);
                         this.editMarker.setLatLng(newLatLng);
                         this.editMap.setView(newLatLng, 15);
                    }
                }
            } catch (error) {
                console.error('Geocoding error:', error);
            }
        },

        // --- FAQ Edit Methods ---
        editingFaqs: false,
        savingFaqs: false,
        faqItems: <?php echo json_encode($faqs ?? []); ?>,
        
        addFaq() {
            this.faqItems.push({ question: '', answer: '' });
        },
        
        removeFaq(index) {
            this.faqItems.splice(index, 1);
        },
        
        async saveFaqs() {
             this.savingFaqs = true;
             const fd = new FormData();
             fd.append('faqs', JSON.stringify(this.faqItems));
             
             try {
                const response = await fetch('<?php echo BASE_URL; ?>event/<?php echo $event['id']; ?>/update-faqs', {
                    method: 'POST',
                    body: fd
                });
                
                const data = await response.json();
                
                if (data.status === 'success') {
                    this.editingFaqs = false;
                    if (typeof window.showToast === 'function') {
                        window.showToast('success', 'FAQs updated successfully!', 'Success');
                    } else {
                        alert('FAQs updated successfully!');
                    }
                    setTimeout(() => window.location.reload(), 1000);
                } else {
                    if (typeof window.showToast === 'function') {
                        window.showToast('error', data.message || 'Failed to update FAQs', 'Error');
                    } else {
                        alert(data.message || 'Failed to update FAQs');
                    }
                }
            } catch (error) {
                console.error('Error updating FAQs:', error);
                if (typeof window.showToast === 'function') {
                    window.showToast('error', 'An error occurred. Please try again.', 'Error');
                } else {
                    alert('An error occurred. Please try again.');
                }
            } finally {
                this.savingFaqs = false;
            }
        }
    }
}
</script>

<style>
.custom-scrollbar::-webkit-scrollbar { width: 4px; }
.custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
.custom-scrollbar::-webkit-scrollbar-thumb { background: #9CA3AF; border-radius: 10px; }
.active-tier {
    border-color: #8B7355 !important;
    background: rgba(139, 115, 85, 0.1);
}
</style>

<!-- Success Modal -->
<div x-show="showSuccessModal" style="display: none;" class="fixed inset-0 z-[60] flex items-center justify-center px-4">
    <div class="absolute inset-0 bg-black/40 backdrop-blur-sm" @click="showSuccessModal = false" x-transition.opacity></div>
    <div class="bg-white rounded-3xl p-8 max-w-sm w-full relative shadow-2xl transform transition-all text-center"
         x-transition:enter="ease-out duration-300"
         x-transition:enter-start="opacity-0 scale-90 translate-y-4"
         x-transition:enter-end="opacity-100 scale-100 translate-y-0"
         x-transition:leave="ease-in duration-200"
         x-transition:leave-start="opacity-100 scale-100 translate-y-0"
         x-transition:leave-end="opacity-0 scale-90 translate-y-4">
        
        <div class="w-20 h-20 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-6 animate-bounce">
            <i data-lucide="check" class="w-10 h-10 text-green-600"></i>
        </div>
        
        <h3 class="text-2xl font-bold text-charcoal-900 mb-2">Request Sent!</h3>
        <p class="text-charcoal-600 mb-8 leading-relaxed">
            Your request has been sent to the host. You will be notified once it is approved.
        </p>
        
        <button @click="showSuccessModal = false" class="w-full bg-brand-600 hover:bg-brand-700 text-white font-bold py-3.5 rounded-xl shadow-lg transition transform hover:scale-[1.02]">
            Got it, thanks!
        </button>
    </div>
</div>
