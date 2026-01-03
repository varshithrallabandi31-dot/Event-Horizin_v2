<!-- Context-Aware Chatbot Widget - Conversational Style -->
<div x-data="chatbotWidget()" 
     x-init="init()"
     class="fixed bottom-6 right-6 z-50"
     @keydown.escape.window="isOpen = false">
    
    <!-- Floating Chat Icon Button -->
    <button @click="toggleChat()" 
            class="group relative w-14 h-14 bg-gradient-to-tr from-brand-500 to-brand-600 hover:from-brand-600 hover:to-brand-700 text-white rounded-full shadow-2xl shadow-brand-500/50 hover:shadow-brand-600/60 transition-all duration-300 transform hover:scale-110 flex items-center justify-center"
            :class="{ 'scale-0': isOpen }"
            aria-label="Open help chat">
        <i data-lucide="message-circle" class="w-6 h-6"></i>
        
        <!-- Notification Badge -->
        <span class="absolute -top-1 -right-1 w-5 h-5 bg-red-500 text-white text-xs font-bold rounded-full flex items-center justify-center border-2 border-white dark:border-charcoal-950 animate-pulse"
              x-show="!isOpen && !hasInteracted"
              x-text="currentQuestions.length"></span>
    </button>
    
    <!-- Chat Panel -->
    <div x-show="isOpen" x-cloak
         x-transition:enter="transition ease-out duration-300 transform"
         x-transition:enter-start="translate-y-8 opacity-0 scale-95"
         x-transition:enter-end="translate-y-0 opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-200 transform"
         x-transition:leave-start="translate-y-0 opacity-100 scale-100"
         x-transition:leave-end="translate-y-8 opacity-0 scale-95"
         class="absolute bottom-0 right-0 w-[380px] max-w-[calc(100vw-3rem)] h-[550px] max-h-[calc(100vh-8rem)] bg-white dark:bg-charcoal-900 rounded-2xl shadow-2xl border border-charcoal-200 dark:border-charcoal-700 flex flex-col overflow-hidden"
         @click.away="isOpen = false">
        
        <!-- Header -->
        <div class="bg-gradient-to-r from-brand-500 to-brand-600 p-4 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-white/20 backdrop-blur-sm rounded-full flex items-center justify-center">
                    <i data-lucide="bot" class="w-5 h-5 text-white"></i>
                </div>
                <div>
                    <h3 class="text-white font-bold text-lg">EventBot</h3>
                    <p class="text-white/80 text-xs flex items-center gap-1">
                        <span class="w-2 h-2 bg-green-400 rounded-full animate-pulse"></span>
                        Online
                    </p>
                </div>
            </div>
            <button @click="toggleChat()" 
                    class="text-white/80 hover:text-white transition p-1 hover:bg-white/10 rounded-lg"
                    aria-label="Close chat">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>
        
        <!-- Chat Messages Area -->
        <div class="flex-1 overflow-y-auto p-4 space-y-4 bg-cream-50 dark:bg-charcoal-950">
            
            <!-- Welcome Message (Bot) -->
            <div class="flex items-start gap-2 animate-fade-in">
                <div class="w-8 h-8 bg-gradient-to-tr from-brand-500 to-brand-600 rounded-full flex items-center justify-center flex-shrink-0">
                    <i data-lucide="bot" class="w-4 h-4 text-white"></i>
                </div>
                <div class="flex-1">
                    <div class="bg-white dark:bg-charcoal-800 rounded-2xl rounded-tl-sm p-3 shadow-sm border border-charcoal-100 dark:border-charcoal-700">
                        <p class="text-charcoal-800 dark:text-charcoal-200 text-sm">
                            👋 Hi! I'm EventBot. How can I help you today?
                        </p>
                    </div>
                    <p class="text-xs text-charcoal-400 dark:text-charcoal-500 mt-1 ml-2">Just now</p>
                </div>
            </div>
            
            <!-- Quick Questions (Bot) -->
            <div class="flex items-start gap-2" x-show="!selectedQA">
                <div class="w-8 h-8 bg-gradient-to-tr from-brand-500 to-brand-600 rounded-full flex items-center justify-center flex-shrink-0">
                    <i data-lucide="bot" class="w-4 h-4 text-white"></i>
                </div>
                <div class="flex-1">
                    <div class="bg-white dark:bg-charcoal-800 rounded-2xl rounded-tl-sm p-3 shadow-sm border border-charcoal-100 dark:border-charcoal-700">
                        <p class="text-charcoal-800 dark:text-charcoal-200 text-sm mb-3">
                            Here are some questions I can help with:
                        </p>
                        
                        <!-- Question Buttons -->
                        <div class="space-y-2">
                            <template x-for="(qa, index) in currentQuestions" :key="index">
                                <button @click="selectQuestion(qa)" 
                                        class="w-full text-left px-3 py-2.5 rounded-xl bg-brand-50 dark:bg-brand-900/20 hover:bg-brand-100 dark:hover:bg-brand-900/30 border border-brand-200 dark:border-brand-800 hover:border-brand-400 dark:hover:border-brand-600 transition-all group">
                                    <div class="flex items-center gap-2">
                                        <i data-lucide="message-square" class="w-4 h-4 text-brand-600 dark:text-brand-400 flex-shrink-0 group-hover:scale-110 transition-transform"></i>
                                        <span class="text-charcoal-800 dark:text-charcoal-200 text-sm font-medium" 
                                              x-text="qa.question"></span>
                                    </div>
                                </button>
                            </template>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- User Question (when selected) -->
            <div x-show="selectedQA !== null" class="flex items-start gap-2 justify-end">
                <div class="flex-1 flex justify-end">
                    <div class="bg-gradient-to-r from-brand-500 to-brand-600 rounded-2xl rounded-tr-sm p-3 shadow-sm max-w-[85%]">
                        <p class="text-white text-sm font-medium" x-text="selectedQA?.question"></p>
                    </div>
                </div>
                <div class="w-8 h-8 bg-charcoal-200 dark:bg-charcoal-700 rounded-full flex items-center justify-center flex-shrink-0">
                    <i data-lucide="user" class="w-4 h-4 text-charcoal-600 dark:text-charcoal-300"></i>
                </div>
            </div>
            
            <!-- Bot Answer (when question selected) -->
            <div x-show="selectedQA !== null" class="flex items-start gap-2">
                <div class="w-8 h-8 bg-gradient-to-tr from-brand-500 to-brand-600 rounded-full flex items-center justify-center flex-shrink-0">
                    <i data-lucide="bot" class="w-4 h-4 text-white"></i>
                </div>
                <div class="flex-1">
                    <div class="bg-white dark:bg-charcoal-800 rounded-2xl rounded-tl-sm p-3 shadow-sm border border-charcoal-100 dark:border-charcoal-700">
                        <div class="text-charcoal-700 dark:text-charcoal-300 text-sm leading-relaxed" 
                             x-html="selectedQA?.answer"></div>
                    </div>
                    <p class="text-xs text-charcoal-400 dark:text-charcoal-500 mt-1 ml-2">Just now</p>
                </div>
            </div>
            
            <!-- Ask Another Question Button -->
            <div x-show="selectedQA !== null" class="flex justify-center pt-2">
                <button @click="selectedQA = null; scrollToBottom()" 
                        class="px-4 py-2 bg-brand-100 dark:bg-brand-900/30 hover:bg-brand-200 dark:hover:bg-brand-900/50 text-brand-700 dark:text-brand-300 rounded-full text-sm font-semibold transition-all flex items-center gap-2 border border-brand-300 dark:border-brand-700">
                    <i data-lucide="arrow-left" class="w-4 h-4"></i>
                    Ask another question
                </button>
            </div>
            
            <!-- Scroll anchor -->
            <div x-ref="scrollAnchor"></div>
        </div>
        
        <!-- Footer -->
        <div class="border-t border-charcoal-200 dark:border-charcoal-700 p-3 bg-white dark:bg-charcoal-900">
            <div class="flex items-center gap-2 mb-2">
                <div class="flex-1 bg-charcoal-100 dark:bg-charcoal-800 rounded-full px-4 py-2 text-sm text-charcoal-400 dark:text-charcoal-500 flex items-center gap-2">
                    <i data-lucide="message-circle" class="w-4 h-4"></i>
                    <span>Select a question above...</span>
                </div>
            </div>
            <p class="text-xs text-charcoal-500 dark:text-charcoal-400 text-center">
                Need more help? <a href="mailto:support@eventhorizons.com" class="text-brand-600 dark:text-brand-400 hover:underline font-semibold">Contact Support</a>
            </p>
        </div>
    </div>
</div>

<style>
[x-cloak] { display: none !important; }

@keyframes fade-in {
    from {
        opacity: 0;
        transform: translateY(10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.animate-fade-in {
    animation: fade-in 0.5s ease-out;
}
</style>

<script>
function chatbotWidget() {
    return {
        isOpen: false,
        selectedQA: null,
        currentPath: '',
        hasInteracted: false,
        
        // Questions database organized by page path
        questionsDB: {
            '/login': [
                {
                    question: "How do I login to the app?",
                    answer: "Enter your phone number and click <strong>'Send OTP'</strong>. You'll receive a one-time password via SMS. Enter the OTP to verify and login to your account. 📱"
                },
                {
                    question: "What is the phone verification process?",
                    answer: "We use phone-based authentication for security. After entering your phone number, you'll receive an OTP (One-Time Password) via SMS. Enter this code to verify your identity and access your account. 🔐"
                },
                {
                    question: "I didn't receive my OTP, what should I do?",
                    answer: "If you didn't receive the OTP:<br>• Check your phone's SMS inbox<br>• Wait 1-2 minutes (delivery may be delayed)<br>• Try requesting a new OTP<br>• Ensure you entered the correct phone number ✅"
                },
                {
                    question: "Can I create an account?",
                    answer: "Yes! Simply enter your phone number on the login page. If you're a new user, you'll be prompted to complete your profile after OTP verification. Provide your name, email, and interests to get started. 🎉"
                }
            ],
            
            '/home': [
                {
                    question: "How do I discover events?",
                    answer: "Browse featured events on the home page or click <strong>'Explore'</strong> in the navigation menu to see all available events. You can filter by category, date, and location to find events that interest you. 🎪"
                },
                {
                    question: "How can I create an event?",
                    answer: "Click <strong>'Create Event'</strong> in the navigation menu. You'll be guided through a multi-step wizard to add event details, location, ticket tiers, seating (optional), and customize your event kit. 🎨"
                },
                {
                    question: "What is EventHorizon?",
                    answer: "EventHorizon is a modern event management platform that helps you discover amazing events, connect with communities, and host unforgettable experiences. Features include digital event kits, QR check-in, real-time chat, and comprehensive analytics. ✨"
                },
                {
                    question: "How do I RSVP to an event?",
                    answer: "Click on any event card to view details. On the event page, click the <strong>'RSVP Now'</strong> button, select your ticket tier, choose a seat (if applicable), and complete the registration form. You'll receive a confirmation and digital event kit upon approval. 🎟️"
                }
            ],
            
            '/explore': [
                {
                    question: "How do I search for events?",
                    answer: "Use the search bar at the top of the Explore page to search by event name, description, or location. Results update in real-time as you type. 🔍"
                },
                {
                    question: "How do I filter events by category?",
                    answer: "Click on category tags or use the filter dropdown to narrow down events by type (Music, Tech, Sports, etc.). You can also filter by date range and location. 🎯"
                },
                {
                    question: "Can I see event details before RSVPing?",
                    answer: "Absolutely! Click on any event card to view full details including description, date/time, location, ticket pricing, seating map, FAQs, and organizer information before deciding to RSVP. 👀"
                },
                {
                    question: "How do I find events near me?",
                    answer: "Events are displayed with location information. You can filter by city or use the map view (if available) to find events in your area. Each event card shows the city and venue name. 📍"
                }
            ],
            
            '/events/create': [
                {
                    question: "How do I create an event?",
                    answer: "Follow the step-by-step wizard:<br>1️⃣ Enter basic details (title, description, date/time)<br>2️⃣ Add location and map coordinates<br>3️⃣ Upload event image or video<br>4️⃣ Configure ticket tiers and pricing<br>5️⃣ Set up seating (optional)<br>6️⃣ Customize event kit<br>7️⃣ Preview and publish 🚀"
                },
                {
                    question: "What are ticket tiers?",
                    answer: "Ticket tiers let you offer different pricing levels like <strong>Free, Early Bird, VIP,</strong> or <strong>General Admission</strong>. Each tier has its own price and quantity limit. You can create multiple tiers to accommodate different attendee types. 🎫"
                },
                {
                    question: "How do I set up seat selection?",
                    answer: "During event creation, enable <strong>'Has Seating'</strong> option. You can configure rows, columns, sections (VIP, Standard), and pricing per section. Attendees will see an interactive seat map during RSVP. 💺"
                },
                {
                    question: "Can I make my event free?",
                    answer: "Yes! When configuring ticket tiers, set the price to <strong>$0.00</strong> or create a 'Free' tier. Free events don't require payment processing, and attendees can RSVP directly. 🎁"
                },
                {
                    question: "How do I add event images?",
                    answer: "In step 3 of event creation, you can upload an image or video as your event header. Supported formats include <strong>JPG, PNG</strong> for images and <strong>MP4</strong> for videos. Choose a high-quality, eye-catching visual to attract attendees. 📸"
                }
            ],
            
            '/event': [
                {
                    question: "How do I RSVP to this event?",
                    answer: "Scroll down and click the <strong>'RSVP Now'</strong> button. Select your preferred ticket tier, choose a seat if applicable, fill out the registration form with your details, and submit. You'll receive a confirmation email upon approval. ✅"
                },
                {
                    question: "What payment methods are accepted?",
                    answer: "We accept <strong>PayPal</strong> for paid events. After selecting your ticket tier and seat, you'll be redirected to PayPal's secure checkout to complete your payment. Free events don't require payment. 💳"
                },
                {
                    question: "How do I select my seat?",
                    answer: "If the event has seating enabled, you'll see an interactive seat map during RSVP. Click on an available seat (shown in <span style='color: green;'>green</span>) to select it. Selected seats turn <span style='color: blue;'>blue</span>, and booked seats are shown in <span style='color: gray;'>gray</span>. 🪑"
                },
                {
                    question: "Can I chat with other attendees?",
                    answer: "Yes! Once your RSVP is approved, you can access the event chat room. Click the <strong>'Chat'</strong> tab on the event page to join conversations with other attendees and the organizer. 💬"
                },
                {
                    question: "How do I get my event ticket?",
                    answer: "After your RSVP is approved, you'll receive a <strong>digital event kit</strong> via email containing your entry pass with QR code, calendar invite, event details, and certificates. You can also download it from the event page. 🎟️"
                }
            ],
            
            '/organizer/dashboard': [
                {
                    question: "How do I manage RSVPs?",
                    answer: "Click on any event in your dashboard to view all RSVPs. You can see pending, approved, and rejected attendees. Use the action buttons to approve or reject individual RSVPs. 📊"
                },
                {
                    question: "How do I approve or reject attendees?",
                    answer: "In the RSVP management section, click the <span style='color: green;'>✓ green checkmark</span> to approve or the <span style='color: red;'>✗ red X</span> to reject an attendee. Approved attendees receive a digital event kit via email. You can provide a reason when rejecting. ✋"
                },
                {
                    question: "How do I view event analytics?",
                    answer: "Click the <strong>'Analytics'</strong> button on any event card in your dashboard. You'll see RSVP trends, attendance statistics, 7-day charts, ticket tier distribution, and payment status breakdowns. 📈"
                },
                {
                    question: "How do I send bulk emails to attendees?",
                    answer: "In the event management page, use the <strong>'Send Bulk Email'</strong> feature to compose and send messages to all approved attendees. This is useful for event updates, reminders, or last-minute changes. 📧"
                },
                {
                    question: "How do I delete an event?",
                    answer: "Click the <strong>'Delete Event'</strong> button on the event card or in event settings. Confirm the deletion. <strong style='color: red;'>⚠️ Warning:</strong> This action is permanent and will remove all associated RSVPs, chats, and data. 🗑️"
                }
            ],
            
            '/profile': [
                {
                    question: "How do I update my profile?",
                    answer: "Click the <strong>'Edit Profile'</strong> button, update your name, email, location, bio, or interests, and click <strong>'Save Changes'</strong>. Your profile information is used for event RSVPs and networking. ✏️"
                },
                {
                    question: "How do I change my avatar?",
                    answer: "In the profile edit section, click on your current avatar image and upload a new photo. Supported formats are <strong>JPG and PNG</strong>. Your avatar appears next to your name throughout the platform. 🖼️"
                },
                {
                    question: "How do I view my event history?",
                    answer: "Your profile page displays all events you've attended or organized. Scroll down to see <strong>'Events Attended'</strong> and <strong>'Events Hosted'</strong> sections with complete history and statistics. 📅"
                },
                {
                    question: "What are badges and how do I earn them?",
                    answer: "Badges are achievements you earn by participating in events. Examples include <strong>'Newcomer'</strong> (first event), <strong>'Social Butterfly'</strong> (5 events attended), <strong>'Event Enthusiast'</strong> (10 events), and <strong>'Host with the Most'</strong> (hosted events). They appear on your profile. 🏆"
                }
            ],
            
            '/analytics': [
                {
                    question: "How do I read the analytics dashboard?",
                    answer: "The dashboard shows key metrics: <strong>Total RSVPs, Approved attendees, Pending requests,</strong> and <strong>Revenue</strong>. Charts display RSVP trends over time, ticket tier distribution, and payment status breakdowns. 📊"
                },
                {
                    question: "What do the charts represent?",
                    answer: "The <strong>line chart</strong> shows RSVP submissions over the last 7 days. The <strong>pie chart</strong> shows ticket tier distribution. The <strong>bar chart</strong> displays payment status (completed, pending, failed). Use these insights to track event popularity. 📉"
                },
                {
                    question: "How do I export attendee data?",
                    answer: "Currently, you can view all attendee details in the RSVP management section. For bulk export, use the browser's print function or copy the data. <strong>CSV export feature is coming soon!</strong> 💾"
                },
                {
                    question: "How do I track RSVP trends?",
                    answer: "The 7-day trend chart shows daily RSVP submissions. Look for spikes to identify when promotions are working. The cumulative total helps you track progress toward your attendance goals. 📈"
                }
            ],
            
            '/profile/complete': [
                {
                    question: "What information do I need to provide?",
                    answer: "Complete your profile with your <strong>full name, email address, location</strong> (optional), a short <strong>bio</strong>, and your <strong>interests</strong>. This information helps organizers understand attendees and personalize event experiences. 📝"
                },
                {
                    question: "Why do I need to complete my profile?",
                    answer: "A complete profile is required for RSVPing to events. It helps organizers verify attendees, send event updates via email, and create a better community experience. It's a one-time setup! ⚡"
                },
                {
                    question: "Can I skip this step?",
                    answer: "You can skip for now, but you'll need to complete your profile before RSVPing to any events. We recommend completing it immediately to access all platform features. 🎯"
                }
            ],
            
            // Default fallback questions
            'default': [
                {
                    question: "How do I get started?",
                    answer: "Welcome to EventHorizon! 🎉 Start by browsing events on the <strong>Explore</strong> page, or create your own event using the <strong>'Create Event'</strong> button. Login with your phone number to RSVP and access all features."
                },
                {
                    question: "How do I contact support?",
                    answer: "For assistance, email us at <a href='mailto:support@eventhorizons.com' class='text-brand-600 dark:text-brand-400 hover:underline font-semibold'>support@eventhorizons.com</a>. We typically respond within 24 hours. You can also check our FAQ section for quick answers. 📧"
                },
                {
                    question: "What features does EventHorizon offer?",
                    answer: "EventHorizon offers: <strong>Event discovery & creation, Digital event kits with QR codes, Interactive seat selection, Real-time chat, Live polling, Photo sharing, Analytics dashboard, Badge system,</strong> and <strong>Email notifications</strong>. ✨"
                },
                {
                    question: "How do I navigate the platform?",
                    answer: "Use the top navigation menu to access <strong>Home, Explore, Create Event,</strong> and <strong>Dashboard</strong> (when logged in). Your profile icon in the top-right provides quick access to settings and logout. 🧭"
                }
            ]
        },
        
        init() {
            this.updateCurrentPath();
            // Refresh icons after Alpine initializes
            this.$nextTick(() => {
                if (typeof lucide !== 'undefined') lucide.createIcons();
            });
        },
        
        updateCurrentPath() {
            const path = window.location.pathname;
            const basePath = '<?php echo rtrim(BASE_URL, "/"); ?>';
            
            // Remove base path to get clean route
            this.currentPath = path.replace(basePath, '').replace('/index.php', '') || '/';
            
            // Normalize path
            if (this.currentPath === '') this.currentPath = '/';
        },
        
        get currentQuestions() {
            // Try exact match first
            if (this.questionsDB[this.currentPath]) {
                return this.questionsDB[this.currentPath];
            }
            
            // Try pattern matching for dynamic routes
            if (this.currentPath.includes('/event/') && !this.currentPath.includes('/analytics')) {
                return this.questionsDB['/event'];
            }
            
            if (this.currentPath.includes('/analytics')) {
                return this.questionsDB['/analytics'];
            }
            
            // Check for partial matches
            for (let key in this.questionsDB) {
                if (key !== 'default' && this.currentPath.startsWith(key)) {
                    return this.questionsDB[key];
                }
            }
            
            // Return default questions
            return this.questionsDB['default'];
        },
        
        toggleChat() {
            this.isOpen = !this.isOpen;
            this.hasInteracted = true;
            if (this.isOpen) {
                this.selectedQA = null;
                // Refresh icons when opening
                this.$nextTick(() => {
                    if (typeof lucide !== 'undefined') lucide.createIcons();
                    this.scrollToBottom();
                });
            }
        },
        
        selectQuestion(qa) {
            this.selectedQA = qa;
            // Refresh icons and scroll after selecting question
            this.$nextTick(() => {
                if (typeof lucide !== 'undefined') lucide.createIcons();
                this.scrollToBottom();
            });
        },
        
        scrollToBottom() {
            this.$nextTick(() => {
                if (this.$refs.scrollAnchor) {
                    this.$refs.scrollAnchor.scrollIntoView({ behavior: 'smooth', block: 'end' });
                }
            });
        }
    }
}
</script>
