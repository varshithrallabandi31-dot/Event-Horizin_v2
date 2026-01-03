<!-- Profile Completion Modal -->
<div x-data="profileCheckModal()" x-show="showModal" x-cloak 
    class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm"
    @click.self="showModal = false">
    <div class="bg-white dark:bg-charcoal-900 rounded-3xl shadow-2xl max-w-md w-full mx-4 p-8 space-y-6" @click.stop>
        <div class="text-center">
            <div class="w-16 h-16 bg-brand-100 dark:bg-brand-900/30 rounded-2xl flex items-center justify-center mx-auto mb-4">
                <i data-lucide="user-check" class="w-8 h-8 text-brand-600 dark:text-brand-400"></i>
            </div>
            <h2 class="text-2xl font-bold text-charcoal-900 dark:text-white mb-2">Complete Your Profile</h2>
            <p class="text-charcoal-600 dark:text-charcoal-400 text-sm">Before organizing your first event, we need your name and email to send you event confirmations.</p>
        </div>
        
        <form @submit.prevent="submitProfile" class="space-y-4">
            <div>
                <label class="block text-xs font-bold text-charcoal-600 dark:text-charcoal-400 uppercase tracking-widest mb-2">Your Name *</label>
                <input type="text" x-model="profileData.name" required
                    class="w-full bg-cream-50 dark:bg-charcoal-800 border-2 border-charcoal-200 dark:border-charcoal-700 rounded-xl px-4 py-3 text-charcoal-900 dark:text-white focus:border-brand-500 outline-none transition"
                    placeholder="John Doe">
            </div>
            
            <div>
                <label class="block text-xs font-bold text-charcoal-600 dark:text-charcoal-400 uppercase tracking-widest mb-2">Your Email *</label>
                <input type="email" x-model="profileData.email" required
                    class="w-full bg-cream-50 dark:bg-charcoal-800 border-2 border-charcoal-200 dark:border-charcoal-700 rounded-xl px-4 py-3 text-charcoal-900 dark:text-white focus:border-brand-500 outline-none transition"
                    placeholder="john@example.com">
            </div>
            
            <div x-show="error" class="bg-red-50 dark:bg-red-900/20 border-2 border-red-200 dark:border-red-800 rounded-xl p-3 text-sm text-red-600 dark:text-red-400" x-text="error"></div>
            
            <div class="flex gap-3">
                <button type="submit" :disabled="loading"
                    class="flex-1 bg-brand-600 text-white px-6 py-3 rounded-xl font-bold shadow-lg shadow-brand-500/20 hover:bg-brand-700 transition disabled:opacity-50 disabled:cursor-not-allowed">
                    <span x-show="!loading">Continue</span>
                    <span x-show="loading">Updating...</span>
                </button>
            </div>
        </form>
    </div>
</div>

<div class="max-w-5xl mx-auto px-4 py-20" x-data="createEventForm()">
    
    <!-- Header -->
    <div class="mb-12">
        <h1 class="serif-heading text-4xl md:text-5xl font-bold text-charcoal-900 dark:text-white mb-3">Host a new experience</h1>
        <p class="text-charcoal-600 dark:text-charcoal-400 text-lg">Fill out the form below to create your event</p>
    </div>

    <!-- Single Form Container -->
    <form @submit.prevent="submitEvent" class="space-y-8">
        
        <!-- Basic Information Section -->
        <div class="bg-white dark:bg-charcoal-900 p-6 md:p-8 rounded-[2rem] border-2 border-charcoal-100 dark:border-charcoal-800 shadow-xl shadow-charcoal-900/5 space-y-6">
            <h2 class="text-2xl font-bold text-charcoal-900 dark:text-white flex items-center gap-3">
                <div class="w-10 h-10 bg-brand-100 dark:bg-brand-900/30 rounded-xl flex items-center justify-center">
                    <i data-lucide="info" class="w-5 h-5 text-brand-600 dark:text-brand-400"></i>
                </div>
                Basic Information
            </h2>
            
            <div>
                <label class="block text-xs font-bold text-charcoal-600 dark:text-charcoal-400 uppercase tracking-widest mb-2">Event Title *</label>
                <input type="text" x-model="formData.title" required placeholder="e.g. Summer Solstice Party" 
                    class="w-full bg-cream-50 dark:bg-charcoal-800 border-2 border-charcoal-200 dark:border-charcoal-700 rounded-xl px-4 py-4 text-charcoal-900 dark:text-white focus:border-brand-500 outline-none transition text-xl font-bold placeholder-charcoal-300 dark:placeholder-charcoal-600">
            </div>

            <div>
                <label class="block text-xs font-bold text-charcoal-600 dark:text-charcoal-400 uppercase tracking-widest mb-2">Category *</label>
                <div class="relative">
                    <select x-model="formData.category" required class="w-full bg-cream-50 dark:bg-charcoal-800 border-2 border-charcoal-200 dark:border-charcoal-700 rounded-xl px-4 py-4 text-charcoal-900 dark:text-white focus:border-brand-500 outline-none appearance-none font-medium">
                        <option value="Social">Social</option>
                        <option value="Tech">Tech</option>
                        <option value="Music">Music</option>
                        <option value="Art">Art</option>
                        <option value="Food">Food</option>
                        <option value="Sports">Sports</option>
                        <option value="Business">Business</option>
                    </select>
                    <i data-lucide="chevron-down" class="absolute right-4 top-1/2 -translate-y-1/2 w-5 h-5 text-charcoal-400 pointer-events-none"></i>
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-charcoal-600 dark:text-charcoal-400 uppercase tracking-widest mb-2">Description</label>
                <textarea x-model="formData.description" rows="4" placeholder="Tell attendees what your event is about..." 
                    class="w-full bg-cream-50 dark:bg-charcoal-800 border-2 border-charcoal-200 dark:border-charcoal-700 rounded-xl px-4 py-3 text-charcoal-900 dark:text-white focus:border-brand-500 outline-none transition placeholder-charcoal-300 dark:placeholder-charcoal-600"></textarea>
            </div>

            <div>
                <label class="block text-xs font-bold text-charcoal-600 dark:text-charcoal-400 uppercase tracking-widest mb-2">Cover Media</label>
                
                <div class="flex gap-4 mb-4">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="radio" x-model="formData.header_type" value="image" class="text-brand-600 focus:ring-brand-500">
                        <span class="text-sm font-bold text-charcoal-700 dark:text-charcoal-300">Image</span>
                    </label>
                    <label class="flex items-center gap-2 cursor-pointer">
                         <input type="radio" x-model="formData.header_type" value="video" class="text-brand-600 focus:ring-brand-500">
                         <span class="text-sm font-bold text-charcoal-700 dark:text-charcoal-300">Video</span>
                    </label>
                </div>

                <div class="flex gap-4 items-center">
                    <div class="w-32 h-20 bg-cream-50 dark:bg-charcoal-800 rounded-xl border-2 border-dashed border-charcoal-300 dark:border-charcoal-600 flex items-center justify-center overflow-hidden group hover:border-brand-400 transition cursor-pointer" @click="$refs.fileInput.click()">
                        <template x-if="formData.header_type === 'image' && formData.image">
                            <img :src="formData.image" class="w-full h-full object-cover">
                        </template>
                        <template x-if="formData.header_type === 'video' && formData.image">
                             <video :src="formData.image" class="w-full h-full object-cover" muted loop></video>
                        </template>
                        <template x-if="!formData.image">
                            <i :data-lucide="formData.header_type === 'video' ? 'video' : 'image'" class="text-charcoal-300 dark:text-charcoal-600 group-hover:text-brand-500 transition"></i>
                        </template>
                    </div>
                    
                    <div class="flex-grow space-y-2">
                        <div class="flex gap-2">
                             <input type="text" x-model="formData.image" :placeholder="formData.header_type === 'video' ? 'Video URL or Upload' : 'Image URL or Upload'" 
                                class="flex-1 bg-cream-50 dark:bg-charcoal-800 border-2 border-charcoal-200 dark:border-charcoal-700 rounded-xl px-4 py-2 text-charcoal-900 dark:text-white focus:border-brand-500 outline-none text-sm transition">
                             <button type="button" @click="$refs.fileInput.click()" class="bg-charcoal-100 dark:bg-charcoal-700 text-charcoal-600 dark:text-charcoal-300 px-4 rounded-xl border-2 border-charcoal-200 dark:border-charcoal-600 hover:bg-charcoal-200 dark:hover:bg-charcoal-600 transition font-bold text-xs whitespace-nowrap">
                                Upload File
                             </button>
                             <input type="file" x-ref="fileInput" @change="handleFileUpload($event)" class="hidden" :accept="formData.header_type === 'video' ? 'video/*' : 'image/*'">
                        </div>
                        
                        <div x-show="formData.header_type === 'image'">
                            <button type="button" @click="generateAIImage()" class="text-xs flex items-center gap-2 bg-purple-50 dark:bg-purple-900/20 text-purple-600 dark:text-purple-400 px-4 py-2 rounded-xl border-2 border-purple-100 dark:border-purple-800 hover:border-purple-200 dark:hover:border-purple-700 hover:bg-purple-100 dark:hover:bg-purple-900/30 transition font-bold">
                                <i data-lucide="wand-2" class="w-4 h-4"></i> Generate with AI
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Date, Time & Location Section -->
        <div class="bg-white dark:bg-charcoal-900 p-6 md:p-8 rounded-[2rem] border-2 border-charcoal-100 dark:border-charcoal-800 shadow-xl shadow-charcoal-900/5 space-y-6">
            <h2 class="text-2xl font-bold text-charcoal-900 dark:text-white flex items-center gap-3">
                <div class="w-10 h-10 bg-brand-100 dark:bg-brand-900/30 rounded-xl flex items-center justify-center">
                    <i data-lucide="calendar" class="w-5 h-5 text-brand-600 dark:text-brand-400"></i>
                </div>
                Date, Time & Location
            </h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-charcoal-600 dark:text-charcoal-400 uppercase tracking-widest mb-2">Date *</label>
                    <input type="date" x-model="formData.date" required class="w-full bg-cream-50 dark:bg-charcoal-800 border-2 border-charcoal-200 dark:border-charcoal-700 rounded-xl px-4 py-3 text-charcoal-900 dark:text-white focus:border-brand-500 outline-none transition font-medium">
                </div>
                <div>
                    <label class="block text-xs font-bold text-charcoal-600 dark:text-charcoal-400 uppercase tracking-widest mb-2">Time *</label>
                    <input type="time" x-model="formData.time" required class="w-full bg-cream-50 dark:bg-charcoal-800 border-2 border-charcoal-200 dark:border-charcoal-700 rounded-xl px-4 py-3 text-charcoal-900 dark:text-white focus:border-brand-500 outline-none transition font-medium">
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-charcoal-600 dark:text-charcoal-400 uppercase tracking-widest mb-2">Venue Name & City *</label>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <input type="text" x-model="formData.location_name" required placeholder="Venue (e.g. Central Park)" 
                        class="w-full bg-cream-50 dark:bg-charcoal-800 border-2 border-charcoal-200 dark:border-charcoal-700 rounded-xl px-4 py-3 text-charcoal-900 dark:text-white focus:border-brand-500 outline-none transition font-medium">
                    <input type="text" x-model="formData.city" required placeholder="City (e.g. New York)" 
                        class="w-full bg-cream-50 dark:bg-charcoal-800 border-2 border-charcoal-200 dark:border-charcoal-700 rounded-xl px-4 py-3 text-charcoal-900 dark:text-white focus:border-brand-500 outline-none transition font-medium">
                </div>
            </div>
        </div>

        <!-- Ticket Tiers & Settings Section -->
        <div class="bg-white dark:bg-charcoal-900 p-6 md:p-8 rounded-[2rem] border-2 border-charcoal-100 dark:border-charcoal-800 shadow-xl shadow-charcoal-900/5 space-y-6">
            <h2 class="text-2xl font-bold text-charcoal-900 dark:text-white flex items-center gap-3">
                <div class="w-10 h-10 bg-brand-100 dark:bg-brand-900/30 rounded-xl flex items-center justify-center">
                    <i data-lucide="ticket" class="w-5 h-5 text-brand-600 dark:text-brand-400"></i>
                </div>
                Tickets & Configuration
            </h2>
            
            <div class="flex items-center justify-between">
                <h3 class="font-bold text-charcoal-900 dark:text-white text-lg">Ticket Tiers</h3>
                <button type="button" @click="addTier()" class="text-xs bg-brand-600 text-white px-4 py-2 rounded-xl flex items-center gap-1 font-bold shadow-lg shadow-brand-500/20 hover:bg-brand-700 transition">
                    <i data-lucide="plus" class="w-3 h-3"></i> Add Tier
                </button>
            </div>
            
            <template x-for="(tier, index) in formData.tiers" :key="index">
                <div class="p-4 bg-cream-50 dark:bg-charcoal-800 rounded-2xl border-2 border-charcoal-100 dark:border-charcoal-700 relative group">
                    <button type="button" @click="removeTier(index)" class="absolute top-2 right-2 text-charcoal-400 hover:text-red-500 transition opacity-0 group-hover:opacity-100">
                        <i data-lucide="x-circle" class="w-4 h-4"></i>
                    </button>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <input type="text" x-model="tier.name" placeholder="Tier Name (e.g. VIP)" class="bg-white dark:bg-charcoal-900 border-2 border-charcoal-200 dark:border-charcoal-700 rounded-xl px-4 py-2 text-sm text-charcoal-900 dark:text-white focus:border-brand-500 outline-none font-bold placeholder-charcoal-400 dark:placeholder-charcoal-600">
                        <input type="text" x-model="tier.price" placeholder="Price ($ or 'Free')" class="bg-white dark:bg-charcoal-900 border-2 border-charcoal-200 dark:border-charcoal-700 rounded-xl px-4 py-2 text-sm text-charcoal-900 dark:text-white focus:border-brand-500 outline-none font-mono placeholder-charcoal-400 dark:placeholder-charcoal-600 text-right">
                    </div>
                </div>
            </template>

            <hr class="border-charcoal-100 dark:border-charcoal-800">
            
            <!-- Custom Sections -->
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="font-bold text-charcoal-900 dark:text-white text-lg">Custom Sections</h3>
                    <p class="text-[10px] text-charcoal-500 dark:text-charcoal-400">Add extra tabs like Lineup, Agenda, or Sponsors.</p>
                </div>
                <button type="button" @click="addSection()" class="text-xs bg-purple-600 text-white px-4 py-2 rounded-xl flex items-center gap-1 font-bold shadow-lg shadow-purple-500/20 hover:bg-purple-700 transition">
                    <i data-lucide="plus" class="w-3 h-3"></i> Add Section
                </button>
            </div>

            <template x-for="(section, index) in formData.custom_sections" :key="'s-'+index">
                 <div class="p-4 bg-purple-50 dark:bg-purple-900/20 rounded-2xl border-2 border-purple-100 dark:border-purple-800 relative group space-y-3">
                    <button type="button" @click="removeSection(index)" class="absolute top-2 right-2 text-charcoal-400 hover:text-red-500 transition opacity-0 group-hover:opacity-100">
                        <i data-lucide="x-circle" class="w-4 h-4"></i>
                    </button>
                    <div class="flex gap-2">
                        <input type="text" x-model="section.title" placeholder="Section Title (e.g. Agenda)" class="flex-1 bg-white dark:bg-charcoal-900 border-2 border-purple-200 dark:border-purple-700 rounded-lg px-3 py-2 text-sm font-bold focus:border-purple-500 outline-none">
                        <select x-model="section.type" class="bg-white dark:bg-charcoal-900 border-2 border-purple-200 dark:border-purple-700 rounded-lg px-2 py-2 text-xs font-bold focus:border-purple-500 outline-none">
                            <option value="custom">Standard</option>
                            <option value="lineup">Lineup</option>
                            <option value="agenda">Agenda</option>
                        </select>
                    </div>
                    <textarea x-model="section.content" placeholder="Content (HTML supported)..." rows="3" class="w-full bg-white dark:bg-charcoal-900 border-2 border-purple-200 dark:border-purple-700 rounded-lg px-3 py-2 text-sm focus:border-purple-500 outline-none"></textarea>
                 </div>
            </template>

            <hr class="border-charcoal-100 dark:border-charcoal-800">

            <div class="flex items-center justify-between bg-cream-50 dark:bg-charcoal-800 p-4 rounded-2xl border-2 border-charcoal-100 dark:border-charcoal-700">
                <div>
                   <h3 class="font-bold text-charcoal-900 dark:text-white mb-1 flex items-center gap-2">
                       <i data-lucide="shield-check" class="w-4 h-4 text-brand-600 dark:text-brand-400"></i> Approval Only
                   </h3>
                   <p class="text-xs text-charcoal-500 dark:text-charcoal-400">Guests must be approved before they can join</p>
                </div>
                <button type="button" @click="formData.approval = !formData.approval" 
                    class="w-12 h-7 rounded-full transition relative p-1 shadow-inner"
                    :class="formData.approval ? 'bg-brand-500' : 'bg-charcoal-200 dark:bg-charcoal-700'">
                    <div class="w-5 h-5 bg-white rounded-full transition-transform shadow-sm" :class="formData.approval ? 'translate-x-5' : 'translate-x-0'"></div>
                </button>
            </div>
        </div>

        <!-- Submit Buttons -->
        <div class="flex flex-col sm:flex-row gap-4 items-center justify-end">
            <button type="button" @click="previewEvent()" class="w-full sm:w-auto px-6 py-4 bg-white dark:bg-charcoal-800 text-charcoal-900 dark:text-white border-2 border-charcoal-200 dark:border-charcoal-700 rounded-full font-bold hover:bg-gray-50 dark:hover:bg-charcoal-700 transition flex items-center justify-center gap-2">
                <i data-lucide="eye" class="w-4 h-4"></i> Preview
            </button>
            <button type="submit" class="w-full sm:w-auto px-10 py-4 bg-charcoal-900 dark:bg-brand-600 text-white rounded-full font-bold shadow-xl hover:scale-[1.02] transition flex items-center justify-center gap-2">
                Publish Event <i data-lucide="rocket" class="w-4 h-4"></i>
            </button>
        </div>
    </form>

    <!-- Hidden Form for Preview -->
    <form id="previewForm" action="<?php echo BASE_URL; ?>events/preview" method="POST" target="_blank" enctype="multipart/form-data" style="display:none;">
    </form>

</div>

<script>
function createEventForm() {
    return {
        formData: {
            title: '',
            category: 'Social',
            description: '',
            image: '',
            header_type: 'image',
            date: '',
            time: '',
            location_name: '',
            city: '',
            lat: 40.7128,
            lng: -74.0060,
            tiers: [{name: 'General Admission', price: 'Free'}],
            custom_sections: [],
            approval: true,
            file: null
        },

        init() {
            lucide.createIcons();
            this.$watch('formData.header_type', value => {
                 setTimeout(() => lucide.createIcons(), 100);
            });
        },
        
        handleFileUpload(event) {
            const file = event.target.files[0];
            if (file) {
                if (file.size > 100 * 1024 * 1024) {
                    alert("File is too large. Maximum size is 100MB.");
                    event.target.value = '';
                    return;
                }
                
                this.formData.file = file;
                this.formData.image = URL.createObjectURL(file);
            }
        },

        generateAIImage() {
            this.formData.image = 'https://images.unsplash.com/photo-1533174072545-7a4b6ad7a6c3?auto=format&fit=crop&w=800&q=80';
            window.showToast('success', 'AI Image Generated based on your event title!', 'Success');
        },

        addTier() {
            this.formData.tiers.push({name: '', price: ''});
            setTimeout(() => lucide.createIcons(), 10);
        },

        removeTier(index) {
            if (this.formData.tiers.length > 1) {
                this.formData.tiers.splice(index, 1);
            } else {
                window.showToast('warning', 'You must have at least one ticket tier.', 'Warning');
            }
        },

        addSection() {
            this.formData.custom_sections.push({title: '', content: '', type: 'custom'});
            setTimeout(() => lucide.createIcons(), 10);
        },

        removeSection(index) {
            this.formData.custom_sections.splice(index, 1);
        },

        previewEvent() {
             const form = document.getElementById('previewForm');
             form.innerHTML = '';
             
             const appendInput = (name, value) => {
                 const input = document.createElement('input');
                 input.type = 'hidden';
                 input.name = name;
                 input.value = value;
                 form.appendChild(input);
             };
             
             appendInput('title', this.formData.title);
             appendInput('description', this.formData.description || 'Join us for ' + this.formData.title + '!');
             appendInput('category', this.formData.category);
             appendInput('image', this.formData.image);
             appendInput('header_type', this.formData.header_type);
             appendInput('date', this.formData.date);
             appendInput('time', this.formData.time);
             appendInput('location_name', this.formData.location_name);
             appendInput('city', this.formData.city);
             appendInput('tiers', JSON.stringify(this.formData.tiers));
             appendInput('custom_sections', JSON.stringify(this.formData.custom_sections));
             appendInput('requires_approval', this.formData.approval ? 1 : 0);
             
             if (this.formData.file) {
                 const fileInput = this.$refs.fileInput.cloneNode(true);
                 fileInput.name = 'header_file';
                 form.appendChild(fileInput);
             }
             
             form.submit();
        },

        submitEvent() {
            // Validate required fields
            if (!this.formData.title || !this.formData.date || !this.formData.time || !this.formData.location_name || !this.formData.city) {
                window.showToast('error', 'Please fill in all required fields.', 'Validation Error');
                return;
            }

            const formData = new FormData();
            formData.append('title', this.formData.title);
            formData.append('category', this.formData.category);
            formData.append('image', this.formData.image);
            formData.append('header_type', this.formData.header_type);
            formData.append('date', this.formData.date);
            formData.append('time', this.formData.time);
            formData.append('location_name', this.formData.location_name);
            formData.append('city', this.formData.city);
            formData.append('latitude', this.formData.lat);
            formData.append('longitude', this.formData.lng);
            formData.append('description', this.formData.description || 'Join us for ' + this.formData.title + '! A ' + this.formData.category + ' event.');
            
            if (this.formData.file) {
                formData.append('header_file', this.formData.file);
            }
            
            formData.append('tiers', JSON.stringify(this.formData.tiers));
            formData.append('custom_sections', JSON.stringify(this.formData.custom_sections));
            formData.append('requires_approval', this.formData.approval ? 1 : 0);

            window.showToast('info', 'Creating your event...', 'Please wait');

            fetch('<?php echo BASE_URL; ?>events/create', {
                method: 'POST',
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                if(data.status === 'success') {
                    window.showToast('success', 'Event Published Successfully!', 'Success');
                    setTimeout(() => {
                        window.location.href = data.redirect;
                    }, 1500);
                } else {
                    window.showToast('error', data.message || 'Failed to create event', 'Error');
                }
            })
            .catch(err => {
                console.error(err);
                window.showToast('error', 'Something went wrong. Please try again.', 'Error');
            });
        }
    }
}

function profileCheckModal() {
    return {
        showModal: false,
        loading: false,
        error: '',
        profileData: {
            name: '',
            email: ''
        },
        
        init() {
            this.checkProfile();
        },
        
        async checkProfile() {
            try {
                const response = await fetch('<?php echo BASE_URL; ?>events/checkProfileCompleteness');
                const data = await response.json();
                
                if (data.status === 'success') {
                    if (!data.is_complete) {
                        this.showModal = true;
                        this.profileData.name = data.name || '';
                        this.profileData.email = data.email || '';
                        setTimeout(() => lucide.createIcons(), 100);
                    }
                }
            } catch (err) {
                console.error('Error checking profile:', err);
            }
        },
        
        async submitProfile() {
            this.loading = true;
            this.error = '';
            
            try {
                const formData = new FormData();
                formData.append('name', this.profileData.name);
                formData.append('email', this.profileData.email);
                
                const response = await fetch('<?php echo BASE_URL; ?>events/updateOrganizerProfile', {
                    method: 'POST',
                    body: formData
                });
                
                const data = await response.json();
                
                if (data.status === 'success') {
                    this.showModal = false;
                    window.showToast('success', 'Profile updated successfully!', 'Success');
                } else {
                    this.error = data.message || 'Failed to update profile';
                }
            } catch (err) {
                console.error('Error updating profile:', err);
                this.error = 'An error occurred. Please try again.';
            } finally {
                this.loading = false;
            }
        }
    }
}
</script>
