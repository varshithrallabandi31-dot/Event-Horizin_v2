<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12" x-data="{ 
    currentTab: 'dashboard',
    selectedEvent: 'all'
}">
    <!-- Header with Tabs -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-6 mb-12">
        <div>
            <h1 class="font-serif text-5xl font-bold text-charcoal-900 dark:text-white mb-2">Hey there, <?php echo htmlspecialchars($_SESSION['user_name'] ?? 'Organizer'); ?></h1>
            <p class="text-charcoal-500 dark:text-charcoal-400">Manage your events and plan for what's next.</p>
        </div>
        
        <div class="bg-white dark:bg-charcoal-800 p-1 rounded-full border border-charcoal-200 dark:border-charcoal-700 inline-flex shadow-sm">
            <button @click="currentTab = 'dashboard'" 
                    :class="currentTab === 'dashboard' ? 'bg-brand-500 text-white shadow-md' : 'text-charcoal-600 dark:text-charcoal-400 hover:bg-cream-50 dark:hover:bg-charcoal-700'"
                    class="px-6 py-2 rounded-full text-sm font-bold transition-all duration-300">
                Dashboard
            </button>
            <button @click="currentTab = 'planner'" 
                    :class="currentTab === 'planner' ? 'bg-brand-500 text-white shadow-md' : 'text-charcoal-600 dark:text-charcoal-400 hover:bg-cream-50 dark:hover:bg-charcoal-700'"
                    class="px-6 py-2 rounded-full text-sm font-bold transition-all duration-300 flex items-center gap-2">
                <i data-lucide="calendar" class="w-4 h-4"></i> Planner
            </button>
        </div>
    </div>

    <!-- DASHBOARD VIEW -->
    <div x-show="currentTab === 'dashboard'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform translate-y-4" x-transition:enter-end="opacity-100 transform translate-y-0">
        
        <div class="flex items-center justify-end gap-4 mb-8">
            <button @click="$dispatch('open-qr-scanner')" class="px-5 py-2.5 bg-green-600 text-white rounded-xl font-semibold hover:bg-green-700 transition flex items-center gap-2 shadow-lg shadow-green-600/20">
                <i data-lucide="scan" class="w-4 h-4"></i> Scan QR Code
            </button>
            <button @click="$dispatch('open-mail-modal')" class="px-5 py-2.5 bg-brand-500 text-white rounded-xl font-semibold hover:bg-brand-600 transition flex items-center gap-2 shadow-lg shadow-brand-500/20">
                <i data-lucide="mail" class="w-4 h-4"></i> Send Mail
            </button>
        </div>

        <!-- My Hosted Events Section -->
        <div class="mb-12">
            <h2 class="text-xl font-bold text-charcoal-900 dark:text-white mb-6 flex items-center gap-2">
                <i data-lucide="bar-chart-2" class="text-brand-500"></i> Event Analytics
            </h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <div @click="selectedEvent = 'all'" 
                     :class="selectedEvent === 'all' ? 'border-brand-500 bg-brand-50 dark:bg-brand-900/20 ring-1 ring-brand-500' : 'border-charcoal-200 dark:border-charcoal-700 bg-white dark:bg-charcoal-800 hover:border-brand-300'"
                     class="p-5 rounded-2xl border transition cursor-pointer group shadow-sm">
                    <div class="flex items-center justify-between mb-3">
                        <span class="text-xs font-bold uppercase tracking-widest" :class="selectedEvent === 'all' ? 'text-brand-600' : 'text-charcoal-500'">Overview</span>
                        <i data-lucide="layers" class="w-5 h-5" :class="selectedEvent === 'all' ? 'text-brand-500' : 'text-charcoal-400'"></i>
                    </div>
                    <p class="text-lg font-bold text-charcoal-900 dark:text-white group-hover:text-brand-600 transition">All Requests</p>
                    <p class="text-xs text-charcoal-500 dark:text-charcoal-400 mt-1"><?php echo count($rsvps); ?> Total RSVPs</p>
                </div>
                
                <?php foreach($hostedEvents as $event): ?>
                <div @click="selectedEvent = '<?php echo $event['id']; ?>'" 
                     :class="selectedEvent === '<?php echo $event['id']; ?>' ? 'border-brand-500 bg-brand-50 dark:bg-brand-900/20 ring-1 ring-brand-500' : 'border-charcoal-200 dark:border-charcoal-700 bg-white dark:bg-charcoal-800 hover:border-brand-300'"
                     class="p-5 rounded-2xl border transition cursor-pointer group shadow-sm flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between mb-3">
                            <span class="text-xs font-bold uppercase tracking-widest text-charcoal-500"><?php echo htmlspecialchars($event['category'] ?? 'Event'); ?></span>
                            <a href="<?php echo BASE_URL; ?>event/<?php echo $event['id']; ?>" target="_blank" @click.stop class="text-charcoal-400 hover:text-brand-500 transition">
                                <i data-lucide="external-link" class="w-4 h-4"></i>
                            </a>
                        </div>
                        <p class="text-charcoal-900 dark:text-white font-bold truncate group-hover:text-brand-600 transition"><?php echo htmlspecialchars($event['title'] ?? 'Untitled Event'); ?></p>
                        <p class="text-xs text-charcoal-500 dark:text-charcoal-400 mt-1"><?php echo date('M d', strtotime($event['start_time'])); ?></p>
                    </div>
                    <div class="mt-4 pt-4 border-t border-charcoal-100 dark:border-charcoal-700 flex justify-between items-center bg-transparent">
                        <span class="text-xs font-bold text-charcoal-600 dark:text-charcoal-300"><?php echo $event['rsvp_count']; ?> RSVPs</span>
                        <div class="flex gap-2">
                             <a href="<?php echo BASE_URL; ?>event/<?php echo $event['id']; ?>/analytics" @click.stop class="text-xs font-bold text-brand-600 hover:text-brand-700 dark:hover:text-brand-400 bg-brand-50 dark:bg-brand-900/30 px-2.5 py-1.5 rounded-lg transition hover:bg-brand-100">
                                Analytics
                            </a>
                            <button @click.stop="$dispatch('open-delete-modal', { eventId: <?php echo $event['id']; ?>, eventName: '<?php echo addslashes($event['title']); ?>' })" class="text-xs font-bold text-red-600 hover:text-red-700 dark:hover:text-red-400 bg-red-50 dark:bg-red-900/30 px-2.5 py-1.5 rounded-lg transition hover:bg-red-100">
                                Delete
                            </button>
                            <button @click.stop="$dispatch('open-seating-modal', { eventId: <?php echo $event['id']; ?>, title: '<?php echo addslashes($event['title']); ?>' })" class="text-xs font-bold text-indigo-600 hover:text-indigo-700 dark:hover:text-indigo-400 bg-indigo-50 dark:bg-indigo-900/30 px-2.5 py-1.5 rounded-lg transition hover:bg-indigo-100">
                                Seating
                            </button>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- RSVP Table -->
        <div class="bg-white dark:bg-charcoal-900 rounded-3xl border border-charcoal-200 dark:border-charcoal-700 overflow-hidden shadow-sm">
            <div class="p-6 border-b border-charcoal-200 dark:border-charcoal-700 flex items-center justify-between bg-cream-50/50 dark:bg-charcoal-800/50">
                <h3 class="text-lg font-bold text-charcoal-900 dark:text-white">Recent RSVPs</h3>
                <div class="text-xs font-bold text-charcoal-500 px-3 py-1 bg-white dark:bg-charcoal-700 rounded-full border border-charcoal-200 dark:border-charcoal-600" x-text="selectedEvent === 'all' ? 'All Events' : 'Filtered'"></div>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-gray-50 dark:bg-charcoal-800 text-charcoal-500 dark:text-charcoal-400 text-xs uppercase tracking-wider font-semibold border-b border-charcoal-200 dark:border-charcoal-700">
                            <th class="px-6 py-4">Candidate</th>
                            <th class="px-6 py-4">Event</th>
                            <th class="px-6 py-4">Status</th>
                            <th class="px-6 py-4">Date</th>
                            <th class="px-6 py-4 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-charcoal-100 dark:divide-charcoal-800">
                        <?php foreach($rsvps as $rsvp): ?>
                        <tr class="hover:bg-cream-50 dark:hover:bg-charcoal-800/50 transition group" x-show="selectedEvent === 'all' || selectedEvent === '<?php echo $rsvp['event_id']; ?>'">
                            <td class="px-6 py-4">
                                <div class="font-bold text-charcoal-900 dark:text-white group-hover:text-brand-600 transition"><?php echo htmlspecialchars($rsvp['name'] ?? ''); ?></div>
                                <div class="text-charcoal-500 dark:text-charcoal-400 text-xs mt-0.5"><?php echo htmlspecialchars($rsvp['phone'] ?? ''); ?></div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="text-sm font-medium text-charcoal-700 dark:text-charcoal-300"><?php echo htmlspecialchars($rsvp['event_title'] ?? ''); ?></span>
                            </td>
                            <td class="px-6 py-4">
                                <?php if($rsvp['status'] === 'pending'): ?>
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-yellow-50 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-400 rounded-full text-xs font-bold uppercase tracking-wide border border-yellow-200 dark:border-yellow-800">
                                        <span class="w-1.5 h-1.5 rounded-full bg-yellow-500"></span> Pending
                                    </span>
                                <?php elseif($rsvp['status'] === 'approved'): ?>
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-green-50 text-green-700 dark:bg-green-900/30 dark:text-green-400 rounded-full text-xs font-bold uppercase tracking-wide border border-green-200 dark:border-green-800">
                                        <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span> Approved
                                    </span>
                                <?php else: ?>
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-red-50 text-red-700 dark:bg-red-900/30 dark:text-red-400 rounded-full text-xs font-bold uppercase tracking-wide border border-red-200 dark:border-red-800">
                                        <span class="w-1.5 h-1.5 rounded-full bg-red-500"></span> Rejected
                                    </span>
                                <?php endif; ?>
                            </td>
                            <td class="px-6 py-4 text-charcoal-500 dark:text-charcoal-400 text-sm font-medium">
                                <?php echo date('M d, Y', strtotime($rsvp['created_at'])); ?>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <?php if($rsvp['status'] === 'pending'): ?>
                                    <div class="flex justify-end gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                                        <a href="<?php echo BASE_URL; ?>rsvp/<?php echo $rsvp['id']; ?>/approve" class="p-2 bg-green-100 text-green-700 hover:bg-green-600 hover:text-white rounded-lg transition" title="Approve">
                                            <i data-lucide="check" class="w-4 h-4"></i>
                                        </a>
                                        <a href="<?php echo BASE_URL; ?>rsvp/<?php echo $rsvp['id']; ?>/reject" class="p-2 bg-red-100 text-red-700 hover:bg-red-600 hover:text-white rounded-lg transition" title="Reject">
                                            <i data-lucide="x" class="w-4 h-4"></i>
                                        </a>
                                    </div>
                                <?php else: ?>
                                    <span class="text-charcoal-300 dark:text-charcoal-600 text-xs">—</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <!-- END DASHBOARD VIEW -->

    <!-- PLANNER VIEW -->
    <div x-show="currentTab === 'planner'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform translate-y-4" x-transition:enter-end="opacity-100 transform translate-y-0" style="display: none;">
        
        <?php 
            // Identify Next Event (First one in the sorted list that is in the future)
            $nextEvent = null;
            $now = new DateTime();
            foreach ($hostedEvents as $ev) {
                $start = new DateTime($ev['start_time']);
                if ($start > $now) {
                    $nextEvent = $ev;
                    break;
                }
            }
        ?>

        <?php if($nextEvent): ?>
            <!-- Countdown Card -->
            <?php 
                $target = new DateTime($nextEvent['start_time']);
                $diff = $now->diff($target);
            ?>
            <div class="bg-white dark:bg-charcoal-900 rounded-3xl border border-charcoal-200 dark:border-charcoal-700 p-8 mb-12 shadow-xl shadow-brand-900/5 relative overflow-hidden group">
                <!-- Background Blur/Gradient -->
                <div class="absolute inset-0 bg-gradient-to-br from-brand-50 to-transparent dark:from-brand-900/10 opacity-50"></div>
                
                <h2 class="text-2xl font-bold text-charcoal-900 dark:text-white mb-6 relative z-10">
                    Your event is happening in <span class="text-brand-600 dark:text-brand-500"><?php echo $diff->days; ?> days</span>
                </h2>

                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8 relative z-10">
                    <div class="bg-charcoal-900 text-white rounded-2xl p-6 text-center transform transition group-hover:scale-105 duration-500">
                        <div class="text-5xl font-bold font-mono mb-2"><?php echo str_pad($diff->days, 2, '0', STR_PAD_LEFT); ?></div>
                        <div class="text-xs uppercase tracking-widest text-charcoal-400 font-bold">Days</div>
                    </div>
                    <div class="bg-charcoal-900 text-white rounded-2xl p-6 text-center transform transition group-hover:scale-105 duration-500 delay-75">
                        <div class="text-5xl font-bold font-mono mb-2"><?php echo str_pad($diff->h, 2, '0', STR_PAD_LEFT); ?></div>
                        <div class="text-xs uppercase tracking-widest text-charcoal-400 font-bold">Hours</div>
                    </div>
                    <div class="bg-charcoal-900 text-white rounded-2xl p-6 text-center transform transition group-hover:scale-105 duration-500 delay-100">
                        <div class="text-5xl font-bold font-mono mb-2"><?php echo str_pad($diff->i, 2, '0', STR_PAD_LEFT); ?></div>
                        <div class="text-xs uppercase tracking-widest text-charcoal-400 font-bold">Minutes</div>
                    </div>
                    <div class="bg-charcoal-900 text-white rounded-2xl p-6 text-center transform transition group-hover:scale-105 duration-500 delay-150">
                        <div class="text-5xl font-bold font-mono mb-2"><?php echo str_pad($diff->s, 2, '0', STR_PAD_LEFT); ?></div>
                        <div class="text-xs uppercase tracking-widest text-charcoal-400 font-bold">Seconds</div>
                    </div>
                </div>

                <div class="flex items-center justify-between p-4 bg-cream-50 dark:bg-charcoal-800 rounded-2xl border border-charcoal-200 dark:border-charcoal-700 relative z-10">
                    <div class="flex items-center gap-4">
                        <div class="text-center px-4 py-2 border-r border-charcoal-200 dark:border-charcoal-700">
                            <div class="text-brand-600 dark:text-brand-500 font-bold text-xs uppercase"><?php echo date('M', strtotime($nextEvent['start_time'])); ?></div>
                            <div class="text-charcoal-900 dark:text-white font-bold text-xl"><?php echo date('d', strtotime($nextEvent['start_time'])); ?></div>
                        </div>
                        <img src="<?php echo htmlspecialchars($nextEvent['image_url'] ?? 'https://via.placeholder.com/100'); ?>" class="w-12 h-12 rounded-lg object-cover">
                        <div>
                            <h3 class="font-bold text-charcoal-900 dark:text-white"><?php echo htmlspecialchars($nextEvent['title']); ?></h3>
                            <div class="text-xs text-brand-600 font-bold mt-0.5">
                                On Sale · Starts <?php echo date('M d, Y', strtotime($nextEvent['start_time'])); ?> at <?php echo date('h:i A', strtotime($nextEvent['start_time'])); ?>
                            </div>
                        </div>
                    </div>
                    <div class="text-right hidden sm:block">
                        <div class="text-charcoal-900 dark:text-white font-bold text-lg"><?php echo $nextEvent['rsvp_count']; ?></div>
                        <div class="text-xs text-charcoal-500 dark:text-charcoal-400">Tickets sold</div>
                    </div>
                </div>
            </div>
        <?php else: ?>
            <div class="bg-white dark:bg-charcoal-900 rounded-3xl border border-charcoal-200 dark:border-charcoal-700 p-12 mb-12 text-center">
                <div class="w-16 h-16 bg-cream-100 dark:bg-charcoal-800 rounded-full flex items-center justify-center mx-auto mb-4">
                     <i data-lucide="calendar-off" class="w-8 h-8 text-charcoal-400"></i>
                </div>
                <h2 class="text-xl font-bold text-charcoal-900 dark:text-white mb-2">No Upcoming Events</h2>
                <p class="text-charcoal-500 mb-6">You don't have any upcoming events scheduled. Time to plan something new!</p>
                <a href="<?php echo BASE_URL; ?>events/create" class="inline-flex items-center gap-2 px-6 py-3 bg-brand-500 text-white rounded-xl font-bold hover:bg-brand-600 transition">
                    <i data-lucide="plus-circle" class="w-5 h-5"></i> Create Event
                </a>
            </div>
        <?php endif; ?>

        <!-- Split Events into Upcoming and Completed -->
        <?php 
            $upcomingEvents = [];
            $completedEvents = [];
            $now = new DateTime();
            
            foreach ($hostedEvents as $ev) {
                $start = new DateTime($ev['start_time']);
                if ($start > $now) {
                    $upcomingEvents[] = $ev;
                } else {
                    $completedEvents[] = $ev;
                }
            }
        ?>

        <!-- Upcoming Events Section -->
        <div class="mb-12">
            <h3 class="font-serif text-2xl font-bold text-charcoal-900 dark:text-white mb-6 flex items-center gap-2">
                <i data-lucide="calendar-clock" class="w-6 h-6 text-brand-500"></i>
                Upcoming Events
            </h3>
            <div class="space-y-4">
                <?php if(empty($upcomingEvents)): ?>
                    <div class="bg-white/60 dark:bg-charcoal-900/60 backdrop-blur-xl rounded-2xl border border-charcoal-200 dark:border-charcoal-700 p-8 text-center">
                        <i data-lucide="calendar-plus" class="w-12 h-12 text-charcoal-300 mx-auto mb-3"></i>
                        <p class="text-charcoal-500 dark:text-charcoal-400">No upcoming events. Time to plan something new!</p>
                    </div>
                <?php else: ?>
                    <?php foreach($upcomingEvents as $ev): ?>
                        <div class="bg-white/80 dark:bg-charcoal-900/80 backdrop-blur-xl p-5 rounded-2xl border border-charcoal-200 dark:border-charcoal-700 flex items-center justify-between hover:border-brand-400 hover:shadow-lg hover:shadow-brand-500/10 transition-all group cursor-pointer"
                             @click="window.location.href='<?php echo BASE_URL; ?>event/<?php echo $ev['id']; ?>'">
                            <div class="flex items-center gap-4">
                                 <div class="text-center w-16 bg-gradient-to-br from-brand-400 to-brand-600 rounded-xl p-3 text-white shadow-lg">
                                    <div class="font-bold text-xs uppercase mb-1"><?php echo date('M', strtotime($ev['start_time'])); ?></div>
                                    <div class="font-bold text-2xl leading-none"><?php echo date('d', strtotime($ev['start_time'])); ?></div>
                                </div>
                                <div class="h-12 w-px bg-charcoal-100 dark:bg-charcoal-800"></div>
                                <img src="<?php echo htmlspecialchars($ev['image_url'] ?? 'https://via.placeholder.com/100'); ?>" class="w-20 h-20 rounded-xl object-cover shadow-md">
                                <div>
                                    <h4 class="font-serif text-xl font-bold text-charcoal-900 dark:text-white group-hover:text-brand-600 transition"><?php echo htmlspecialchars($ev['title']); ?></h4>
                                    <p class="text-sm text-charcoal-500 dark:text-charcoal-400 flex items-center gap-1 mt-1">
                                        <i data-lucide="map-pin" class="w-3 h-3"></i> <?php echo htmlspecialchars($ev['location_name']); ?>
                                    </p>
                                </div>
                            </div>
                            <div class="flex items-center gap-6">
                               <span class="px-4 py-2 bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400 rounded-full text-xs font-bold uppercase hidden sm:block">On Sale</span>
                                
                                <div class="text-right hidden sm:block">
                                    <div class="text-charcoal-900 dark:text-white font-bold text-lg"><?php echo $ev['rsvp_count']; ?></div>
                                    <div class="text-xs text-charcoal-500 uppercase tracking-wide">Sold</div>
                                </div>
                                
                                <div class="w-10 h-10 rounded-full bg-cream-100 dark:bg-charcoal-800 flex items-center justify-center text-charcoal-400 group-hover:bg-brand-500 group-hover:text-white transition">
                                    <i data-lucide="chevron-right" class="w-5 h-5"></i>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>

        <!-- Completed Events Section -->
        <div>
            <h3 class="font-serif text-2xl font-bold text-charcoal-900 dark:text-white mb-6 flex items-center gap-2">
                <i data-lucide="check-circle" class="w-6 h-6 text-charcoal-500"></i>
                Completed Events
            </h3>
            <div class="space-y-4">
                <?php if(empty($completedEvents)): ?>
                    <div class="bg-white/60 dark:bg-charcoal-900/60 backdrop-blur-xl rounded-2xl border border-charcoal-200 dark:border-charcoal-700 p-8 text-center">
                        <i data-lucide="archive" class="w-12 h-12 text-charcoal-300 mx-auto mb-3"></i>
                        <p class="text-charcoal-500 dark:text-charcoal-400">No completed events yet.</p>
                    </div>
                <?php else: ?>
                    <?php foreach($completedEvents as $ev): ?>
                        <div class="bg-white/80 dark:bg-charcoal-900/80 backdrop-blur-xl p-5 rounded-2xl border border-charcoal-200 dark:border-charcoal-700 hover:border-charcoal-300 hover:shadow-md transition-all group">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-4 flex-1 cursor-pointer" @click="window.location.href='<?php echo BASE_URL; ?>event/<?php echo $ev['id']; ?>'">
                                     <div class="text-center w-16 bg-charcoal-100 dark:bg-charcoal-800 rounded-xl p-3 text-charcoal-600 dark:text-charcoal-400">
                                        <div class="font-bold text-xs uppercase mb-1"><?php echo date('M', strtotime($ev['start_time'])); ?></div>
                                        <div class="font-bold text-2xl leading-none"><?php echo date('d', strtotime($ev['start_time'])); ?></div>
                                    </div>
                                    <div class="h-12 w-px bg-charcoal-100 dark:bg-charcoal-800"></div>
                                    <img src="<?php echo htmlspecialchars($ev['image_url'] ?? 'https://via.placeholder.com/100'); ?>" class="w-20 h-20 rounded-xl object-cover shadow-md opacity-75">
                                    <div>
                                        <h4 class="font-serif text-xl font-bold text-charcoal-700 dark:text-charcoal-300"><?php echo htmlspecialchars($ev['title']); ?></h4>
                                        <p class="text-sm text-charcoal-500 dark:text-charcoal-400 flex items-center gap-1 mt-1">
                                            <i data-lucide="map-pin" class="w-3 h-3"></i> <?php echo htmlspecialchars($ev['location_name']); ?>
                                        </p>
                                    </div>
                                </div>
                                
                                <!-- Action Buttons for Completed Events -->
                                <div class="flex items-center gap-3">
                                    <div class="text-right hidden sm:block mr-4">
                                        <div class="text-charcoal-700 dark:text-charcoal-400 font-bold"><?php echo $ev['rsvp_count']; ?></div>
                                        <div class="text-xs text-charcoal-500 uppercase">Attended</div>
                                    </div>
                                    
                                    <button @click.stop="window.location.href='<?php echo BASE_URL; ?>event/<?php echo $ev['id']; ?>#polls'" 
                                            class="px-4 py-2 bg-indigo-100 text-indigo-700 dark:bg-indigo-900/30 dark:text-indigo-400 rounded-lg text-sm font-bold hover:bg-indigo-200 dark:hover:bg-indigo-900/50 transition flex items-center gap-2"
                                            title="Create a poll for this event">
                                        <i data-lucide="bar-chart-3" class="w-4 h-4"></i>
                                        <span class="hidden md:inline">Poll</span>
                                    </button>
                                    
                                    <button @click.stop="$dispatch('open-feedback-modal', { eventId: <?php echo $ev['id']; ?>, eventTitle: '<?php echo addslashes($ev['title']); ?>' })" 
                                            class="px-4 py-2 bg-purple-100 text-purple-700 dark:bg-purple-900/30 dark:text-purple-400 rounded-lg text-sm font-bold hover:bg-purple-200 dark:hover:bg-purple-900/50 transition flex items-center gap-2"
                                            title="Request feedback from attendees">
                                        <i data-lucide="message-square" class="w-4 h-4"></i>
                                        <span class="hidden md:inline">Feedback</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <!-- END PLANNER VIEW -->

    <!-- Modals (Copied from original) -->
    <!-- Bulk Mail Modal -->
    <div x-data="{ open: false }" @open-mail-modal.window="open = true" x-show="open" class="fixed inset-0 z-[60] flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm" style="display: none;">
        <div @click.away="open = false" class="bg-white/95 dark:bg-charcoal-900/95 w-full max-w-2xl rounded-3xl border-2 border-charcoal-200 dark:border-charcoal-700 shadow-2xl overflow-hidden backdrop-blur-xl">
            <div class="p-8">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-2xl font-bold text-charcoal-900 dark:text-white">Send Bulk Email</h2>
                    <button @click="open = false" class="text-charcoal-400 hover:text-charcoal-900 transition">
                        <i data-lucide="x" class="w-6 h-6"></i>
                    </button>
                </div>
                <form action="<?php echo BASE_URL; ?>organizer/send-bulk-mail" method="POST">
                    <div class="mb-6">
                        <label class="block text-sm font-semibold text-charcoal-700 mb-2">Subject</label>
                        <input type="text" name="subject" required class="w-full px-4 py-3 bg-cream-50 rounded-xl border-2 border-charcoal-200 text-charcoal-900 focus:ring-2 focus:ring-brand-500 focus:border-brand-500 outline-none transition" placeholder="Important update about the event">
                    </div>
                    <div class="mb-8">
                        <label class="block text-sm font-semibold text-charcoal-700 mb-2">Message</label>
                        <textarea name="message" required rows="6" class="w-full px-4 py-3 bg-cream-50 rounded-xl border-2 border-charcoal-200 text-charcoal-900 focus:ring-2 focus:ring-brand-500 focus:border-brand-500 outline-none transition" placeholder="Write your message here..."></textarea>
                    </div>
                    <div class="flex justify-end gap-4">
                        <button type="button" @click="open = false" class="px-6 py-3 text-charcoal-600 font-semibold hover:text-brand-600 transition">Cancel</button>
                        <button type="submit" class="px-8 py-3 bg-brand-500 text-white font-bold rounded-xl hover:bg-brand-600 transition transform hover:scale-105">
                            Send to All Candidates
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <!-- Feedback Request Modal -->
    <div x-data="{ open: false, eventId: null, eventTitle: '', sending: false }" 
         @open-feedback-modal.window="open = true; eventId = $event.detail.eventId; eventTitle = $event.detail.eventTitle" 
         x-show="open" 
         class="fixed inset-0 z-[60] flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm" 
         style="display: none;">
        <div @click.away="open = false" class="bg-white/95 dark:bg-charcoal-900/95 w-full max-w-2xl rounded-3xl border-2 border-charcoal-200 dark:border-charcoal-700 shadow-2xl overflow-hidden backdrop-blur-xl">
            <div class="p-8">
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <h2 class="font-serif text-2xl font-bold text-charcoal-900 dark:text-white">Request Feedback</h2>
                        <p class="text-sm text-charcoal-500 dark:text-charcoal-400 mt-1" x-text="'For: ' + eventTitle"></p>
                    </div>
                    <button @click="open = false" class="text-charcoal-400 hover:text-charcoal-900 dark:hover:text-white transition">
                        <i data-lucide="x" class="w-6 h-6"></i>
                    </button>
                </div>
                
                <form @submit.prevent="
                    sending = true;
                    fetch('<?php echo BASE_URL; ?>event/' + eventId + '/send-feedback', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                        body: 'message=' + encodeURIComponent($refs.feedbackMessage.value)
                    })
                    .then(r => r.json())
                    .then(res => {
                        sending = false;
                        if(res.status === 'success') {
                            alert('Feedback request sent to ' + res.emailed_count + ' attendees!');
                            open = false;
                            $refs.feedbackMessage.value = '';
                        } else {
                            alert('Error: ' + (res.message || 'Failed to send'));
                        }
                    })
                    .catch(err => {
                        sending = false;
                        alert('Error: ' + err.message);
                    })
                ">
                    <div class="mb-6">
                        <label class="block text-sm font-semibold text-charcoal-700 dark:text-charcoal-300 mb-2">Your Message</label>
                        <textarea x-ref="feedbackMessage" required rows="6" 
                                  class="w-full px-4 py-3 bg-cream-50 dark:bg-charcoal-800 rounded-xl border-2 border-charcoal-200 dark:border-charcoal-700 text-charcoal-900 dark:text-white focus:ring-2 focus:ring-purple-500 focus:border-purple-500 outline-none transition" 
                                  placeholder="Thank you for attending! We'd love to hear your thoughts about the event..."></textarea>
                        <p class="text-xs text-charcoal-500 dark:text-charcoal-400 mt-2">
                            <i data-lucide="info" class="w-3 h-3 inline"></i> This message will be posted to the event chat and emailed to all attendees.
                        </p>
                    </div>
                    <div class="flex justify-end gap-4">
                        <button type="button" @click="open = false" class="px-6 py-3 text-charcoal-600 dark:text-charcoal-400 font-semibold hover:text-brand-600 transition">Cancel</button>
                        <button type="submit" :disabled="sending" class="px-8 py-3 bg-purple-600 text-white font-bold rounded-xl hover:bg-purple-700 transition transform hover:scale-105 disabled:opacity-50 disabled:cursor-not-allowed flex items-center gap-2">
                            <i data-lucide="send" class="w-4 h-4" x-show="!sending"></i>
                            <span x-show="!sending">Send Feedback Request</span>
                            <span x-show="sending">Sending...</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <!-- Delete Event Confirmation Modal -->
    <div x-data="{ open: false, eventId: null, eventName: '' }" @open-delete-modal.window="open = true; eventId = $event.detail.eventId; eventName = $event.detail.eventName" x-show="open" class="fixed inset-0 z-[60] flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm" style="display: none;">
        <div @click.away="open = false" class="bg-white w-full max-w-md rounded-3xl border-2 border-red-200 shadow-2xl overflow-hidden">
            <div class="p-8">
                <div class="flex items-center justify-center mb-6">
                    <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center">
                        <i data-lucide="alert-triangle" class="w-8 h-8 text-red-600"></i>
                    </div>
                </div>
                <h2 class="text-2xl font-bold text-charcoal-900 text-center mb-4">Delete Event?</h2>
                <p class="text-charcoal-600 text-center mb-2">Are you sure you want to delete <strong x-text="eventName"></strong>?</p>
                <div class="bg-red-50 border-l-4 border-red-500 p-4 mb-6 rounded-lg">
                    <p class="text-sm text-red-700">
                        <strong>Warning:</strong> This action cannot be undone. All registered users will receive a cancellation email with refund information.
                    </p>
                </div>
                <div class="flex gap-4">
                    <button @click="open = false" class="flex-1 px-6 py-3 text-charcoal-600 font-semibold border-2 border-charcoal-200 rounded-xl hover:bg-charcoal-50 transition">
                        Cancel
                    </button>
                    <a :href="'<?php echo BASE_URL; ?>event/' + eventId + '/delete'" class="flex-1 px-6 py-3 bg-red-600 text-white font-bold rounded-xl hover:bg-red-700 transition text-center">
                        Delete Event
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Configure Seating Modal -->
    <div x-data="{ open: false, eventId: null, title: '' }" @open-seating-modal.window="open = true; eventId = $event.detail.eventId; title = $event.detail.title" x-show="open" class="fixed inset-0 z-[60] flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm" style="display: none;">
        <div @click.away="open = false" class="bg-white dark:bg-charcoal-900 w-full max-w-lg rounded-3xl border-2 border-charcoal-200 dark:border-charcoal-700 shadow-2xl overflow-hidden">
            <div class="p-8">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-2xl font-bold text-charcoal-900 dark:text-white">Configure Seating</h2>
                    <button @click="open = false" class="text-charcoal-400 hover:text-charcoal-900 dark:hover:text-white transition">
                        <i data-lucide="x" class="w-6 h-6"></i>
                    </button>
                </div>
                <p class="text-charcoal-500 dark:text-charcoal-400 mb-6">Design a seating chart for <strong x-text="title" class="text-charcoal-900 dark:text-white"></strong>.</p>
                
                <form @submit.prevent="
                    fetch('<?php echo BASE_URL; ?>event/configure-seating', {
                        method: 'POST',
                        body: new FormData($event.target)
                    })
                    .then(r => r.json())
                    .then(res => {
                        if(res.status === 'success') {
                            alert('Seating configured successfully!');
                            open = false;
                        } else {
                            alert('Error: ' + (res.message || 'Failed'));
                        }
                    })
                ">
                    <input type="hidden" name="event_id" :value="eventId">
                    
                    <div class="grid grid-cols-2 gap-4 mb-4">
                        <div>
                            <label class="block text-sm font-bold text-charcoal-700 dark:text-charcoal-300 mb-1">Rows</label>
                            <input type="number" name="rows" value="10" min="1" max="26" class="w-full px-3 py-2 bg-cream-50 dark:bg-charcoal-800 rounded-lg border border-charcoal-200 dark:border-charcoal-700 text-charcoal-900 dark:text-white font-bold">
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-charcoal-700 dark:text-charcoal-300 mb-1">Cols</label>
                            <input type="number" name="cols" value="10" min="1" max="20" class="w-full px-3 py-2 bg-cream-50 dark:bg-charcoal-800 rounded-lg border border-charcoal-200 dark:border-charcoal-700 text-charcoal-900 dark:text-white font-bold">
                        </div>
                    </div>
                    
                    <div class="mb-4">
                        <label class="block text-sm font-bold text-charcoal-700 dark:text-charcoal-300 mb-1">Standard Price ($)</label>
                        <input type="number" name="price_standard" value="0" min="0" class="w-full px-3 py-2 bg-cream-50 dark:bg-charcoal-800 rounded-lg border border-charcoal-200 dark:border-charcoal-700 text-charcoal-900 dark:text-white font-bold">
                    </div>
                    
                    <div class="mb-8">
                        <label class="block text-sm font-bold text-charcoal-700 dark:text-charcoal-300 mb-1">VIP Price (First 2 Rows) ($)</label>
                        <input type="number" name="price_vip" value="0" min="0" class="w-full px-3 py-2 bg-cream-50 dark:bg-charcoal-800 rounded-lg border border-charcoal-200 dark:border-charcoal-700 text-charcoal-900 dark:text-white font-bold">
                    </div>

                    <button type="submit" class="w-full py-3 bg-brand-500 text-white font-bold rounded-xl hover:bg-brand-600 transition shadow-lg shadow-brand-500/20">
                        Generate Seating Chart
                    </button>
                    <p class="text-xs text-charcoal-400 text-center mt-3">Warning: This will reset any existing reservations.</p>
                </form>
            </div>
        </div>
    </div>

    <!-- QR Scanner Modal -->
    <div x-data="{ 
        open: false, 
        scanning: false, 
        result: null, 
        error: null,
        html5QrCode: null,
        initScanner() {
            if (!this.html5QrCode) {
                this.html5QrCode = new Html5Qrcode('qr-reader');
            }
            this.startScanning();
        },
        startScanning() {
            this.scanning = true;
            this.result = null;
            this.error = null;
            
            // Try environment (back) camera first, then user (front) camera
            const cameraConfig = { facingMode: 'environment' };
            
            this.html5QrCode.start(
                cameraConfig,
                {
                    fps: 10,
                    qrbox: { width: 250, height: 250 },
                    aspectRatio: 1.0
                },
                (decodedText, decodedResult) => {
                    this.onScanSuccess(decodedText);
                },
                (errorMessage) => {
                    // Ignore scan errors (happens continuously while scanning)
                }
            ).catch(err => {
                console.error('Camera error:', err);
                // Try front camera as fallback
                this.html5QrCode.start(
                    { facingMode: 'user' },
                    {
                        fps: 10,
                        qrbox: { width: 250, height: 250 },
                        aspectRatio: 1.0
                    },
                    (decodedText, decodedResult) => {
                        this.onScanSuccess(decodedText);
                    },
                    (errorMessage) => {
                        // Ignore
                    }
                ).catch(err2 => {
                    this.error = 'Camera access denied or not available. Please allow camera access and try again.';
                    this.scanning = false;
                });
            });
        },
        stopScanning() {
            if (this.html5QrCode && this.scanning) {
                this.html5QrCode.stop().then(() => {
                    this.scanning = false;
                }).catch(err => {
                    console.error('Error stopping scanner:', err);
                });
            }
        },
        onScanSuccess(qrData) {
            this.stopScanning();
            
            // Send to validation endpoint
            fetch('<?php echo BASE_URL; ?>qr/validate', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ qr_data: qrData })
            })
            .then(response => response.json())
            .then(data => {
                this.result = data;
            })
            .catch(err => {
                this.error = 'Validation failed: ' + err.message;
            });
        },
        closeModal() {
            this.stopScanning();
            this.open = false;
            this.result = null;
            this.error = null;
        }
    }" 
    @open-qr-scanner.window="open = true; setTimeout(() => initScanner(), 100)" 
    x-show="open" 
    class="fixed inset-0 z-[60] flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm" 
    style="display: none;">
        <div @click.away="closeModal()" class="bg-white w-full max-w-2xl rounded-3xl border-2 border-charcoal-200 shadow-2xl overflow-hidden">
            <div class="p-8">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-2xl font-bold text-charcoal-900">Scan QR Code</h2>
                    <button @click="closeModal()" class="text-charcoal-400 hover:text-charcoal-900 transition">
                        <i data-lucide="x" class="w-6 h-6"></i>
                    </button>
                </div>

                <!-- Scanner Area -->
                <div x-show="!result && !error" class="mb-6">
                    <div id="qr-reader" class="rounded-xl overflow-hidden border-2 border-charcoal-200"></div>
                    <div class="mt-4 space-y-2">
                        <p class="text-sm text-charcoal-500 text-center font-semibold">📱 Position the QR code within the frame</p>
                        <p class="text-xs text-charcoal-400 text-center">Tip: Use your phone to display the QR code from the PDF or email</p>
                        <p class="text-xs text-charcoal-400 text-center">Hold steady and ensure good lighting for best results</p>
                    </div>
                </div>

                <!-- Success Result -->
                <div x-show="result && result.status === 'success'" class="mb-6">
                    <div class="bg-green-50 border-2 border-green-500 rounded-2xl p-6">
                        <div class="flex items-center justify-center mb-4">
                            <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center">
                                <i data-lucide="check-circle" class="w-8 h-8 text-green-600"></i>
                            </div>
                        </div>
                        <h3 class="text-2xl font-bold text-green-900 text-center mb-2">Entry Validated!</h3>
                        <p class="text-center text-green-700 font-semibold mb-4" x-text="'Event: ' + (result?.event || 'N/A')"></p>
                        <div class="space-y-3">
                            <div class="bg-white rounded-xl p-4">
                                <p class="text-sm text-charcoal-500">Name</p>
                                <p class="text-lg font-bold text-charcoal-900" x-text="result?.user?.name"></p>
                            </div>
                            <div class="bg-white rounded-xl p-4">
                                <p class="text-sm text-charcoal-500">Email</p>
                                <p class="text-lg font-bold text-charcoal-900" x-text="result?.user?.email"></p>
                            </div>
                            <div class="bg-white rounded-xl p-4">
                                <p class="text-sm text-charcoal-500">Phone</p>
                                <p class="text-lg font-bold text-charcoal-900" x-text="result?.user?.phone"></p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Warning Result (Already Checked In) -->
                <div x-show="result && result.status === 'warning'" class="mb-6">
                    <div class="bg-yellow-50 border-2 border-yellow-500 rounded-2xl p-6">
                        <div class="flex items-center justify-center mb-4">
                            <div class="w-16 h-16 bg-yellow-100 rounded-full flex items-center justify-center">
                                <i data-lucide="alert-triangle" class="w-8 h-8 text-yellow-600"></i>
                            </div>
                        </div>
                        <h3 class="text-2xl font-bold text-yellow-900 text-center mb-2">Already Checked In</h3>
                        <p class="text-center text-yellow-700 mb-4" x-text="'Checked in at: ' + (result?.checked_in_at || '')"></p>
                        <div class="space-y-3">
                            <div class="bg-white rounded-xl p-4">
                                <p class="text-sm text-charcoal-500">Name</p>
                                <p class="text-lg font-bold text-charcoal-900" x-text="result?.user?.name"></p>
                            </div>
                            <div class="bg-white rounded-xl p-4">
                                <p class="text-sm text-charcoal-500">Email</p>
                                <p class="text-lg font-bold text-charcoal-900" x-text="result?.user?.email"></p>
                            </div>
                            <div class="bg-white rounded-xl p-4">
                                <p class="text-sm text-charcoal-500">Phone</p>
                                <p class="text-lg font-bold text-charcoal-900" x-text="result?.user?.phone"></p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Error Result -->
                <div x-show="(result && result.status === 'error') || error" class="mb-6">
                    <div class="bg-red-50 border-2 border-red-500 rounded-2xl p-6">
                        <div class="flex items-center justify-center mb-4">
                            <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center">
                                <i data-lucide="x-circle" class="w-8 h-8 text-red-600"></i>
                            </div>
                        </div>
                        <h3 class="text-2xl font-bold text-red-900 text-center mb-2">Invalid User</h3>
                        <p class="text-center text-red-700" x-text="result?.message || error"></p>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex gap-4">
                    <button @click="closeModal()" class="flex-1 px-6 py-3 text-charcoal-600 font-semibold border-2 border-charcoal-200 rounded-xl hover:bg-charcoal-50 transition">
                        Close
                    </button>
                    <button x-show="result || error" @click="result = null; error = null; initScanner()" class="flex-1 px-6 py-3 bg-green-600 text-white font-bold rounded-xl hover:bg-green-700 transition">
                        Scan Another
                    </button>
                </div>
            </div>
        </div>
    </div>

    <?php if(isset($_GET['success'])): ?>
    <div class="mb-8 p-4 bg-green-100 border-2 border-green-400 text-green-700 rounded-2xl">
        <?php if($_GET['success'] === 'mail_sent'): ?>
            <strong>Email Simulated Successfully!</strong><br>
            Since you are on a local server, emails are saved to the <code>public/emails/</code> folder instead of being sent. <a href="<?php echo BASE_URL; ?>public/emails/" target="_blank" class="underline font-bold">View Emails Folder &rarr;</a>
        <?php elseif($_GET['success'] === 'event_deleted'): ?>
            <strong>Event Deleted Successfully!</strong><br>
            The event has been removed and cancellation emails have been sent to all registered users with refund information.
        <?php else: ?>
            Action completed successfully!
        <?php endif; ?>
    </div>
    <?php endif; ?>
                <thead>
                    <tr class="bg-cream-100 text-charcoal-700 text-sm uppercase tracking-wider">
                        <th class="px-6 py-4 font-semibold">Candidate</th>
                        <th class="px-6 py-4 font-semibold">Event</th>
                        <th class="px-6 py-4 font-semibold">Interest</th>
                        <th class="px-6 py-4 font-semibold">Status</th>
                        <th class="px-6 py-4 font-semibold">Date</th>
                        <th class="px-6 py-4 font-semibold text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-charcoal-200">
                    <?php foreach($rsvps as $rsvp): ?>
                    <tr class="hover:bg-cream-50 transition" x-show="selectedEvent === 'all' || selectedEvent === '<?php echo $rsvp['event_id']; ?>'">
                        <td class="px-6 py-4">
                            <div class="font-bold text-charcoal-900 dark:text-white"><?php echo htmlspecialchars($rsvp['name'] ?? ''); ?></div>
                            <div class="text-charcoal-500 dark:text-charcoal-400 text-sm"><?php echo htmlspecialchars($rsvp['phone'] ?? ''); ?></div>
                        </td>
                        <td class="px-6 py-4 text-charcoal-700">
                            <?php echo htmlspecialchars($rsvp['event_title'] ?? ''); ?>
                        </td>
                        <td class="px-6 py-4 text-charcoal-600 text-sm">
                            <?php 
                                $answers = json_decode($rsvp['answers'], true);
                                echo htmlspecialchars($answers['interest'] ?? 'N/A');
                            ?>
                        </td>
                        <td class="px-6 py-4">
                            <?php if($rsvp['status'] === 'pending'): ?>
                                <span class="px-3 py-1 bg-yellow-100 text-yellow-700 rounded-full text-xs font-bold uppercase">Pending</span>
                            <?php elseif($rsvp['status'] === 'approved'): ?>
                                <span class="px-3 py-1 bg-green-100 text-green-700 rounded-full text-xs font-bold uppercase">Approved</span>
                            <?php else: ?>
                                <span class="px-3 py-1 bg-red-100 text-red-700 rounded-full text-xs font-bold uppercase">Rejected</span>
                            <?php endif; ?>
                        </td>
                        <td class="px-6 py-4 text-charcoal-500 text-sm">
                            <?php echo date('M d, Y', strtotime($rsvp['created_at'])); ?>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <?php if($rsvp['status'] === 'pending'): ?>
                                <div class="flex justify-end gap-2">
                                    <a href="<?php echo BASE_URL; ?>rsvp/<?php echo $rsvp['id']; ?>/approve" class="p-2 bg-green-100 text-green-700 hover:bg-green-600 hover:text-white rounded-lg transition">
                                        <i data-lucide="check" class="w-5 h-5"></i>
                                    </a>
                                    <a href="<?php echo BASE_URL; ?>rsvp/<?php echo $rsvp['id']; ?>/reject" class="p-2 bg-red-100 text-red-700 hover:bg-red-600 hover:text-white rounded-lg transition">
                                        <i data-lucide="x" class="w-5 h-5"></i>
                                    </a>
                                </div>
                            <?php else: ?>
                                <span class="text-charcoal-400 text-sm">No actions</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
