<footer class="bg-gradient-to-br from-slate-900 via-blue-900 to-indigo-900 relative overflow-hidden">
    <!-- Background Elements -->
    <div class="absolute inset-0">
        <div class="absolute top-0 left-1/4 w-96 h-96 bg-blue-500/10 rounded-full blur-3xl"></div>
        <div class="absolute bottom-0 right-1/4 w-96 h-96 bg-indigo-500/10 rounded-full blur-3xl"></div>
        <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 w-[600px] h-[600px] bg-gradient-radial from-blue-600/5 to-transparent rounded-full"></div>
    </div>

    <div class="relative max-w-7xl mx-auto py-16 px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-12">
            <!-- Brand Section -->
            <div class="lg:col-span-1">
                <div class="flex items-center mb-6">
                    <div class="w-12 h-12 bg-gradient-to-br from-blue-400 to-indigo-500 rounded-xl flex items-center justify-center mr-4 shadow-lg">
                        <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-2xl font-bold text-white">AssetLab</h3>
                        <div class="w-8 h-0.5 bg-gradient-to-r from-blue-400 to-indigo-400 rounded-full mt-1"></div>
                    </div>
                </div>

                <p class="text-gray-300 mb-6 leading-relaxed">
                    Integrated equipment tracking, laboratory reservations, and automated academic resource management for educational institutions.
                </p>
            </div>

            <!-- Quick Links -->
            <div class="lg:col-span-1">
                <h4 class="text-lg font-semibold text-white mb-6 relative">
                    Platform
                    <div class="absolute bottom-0 left-0 w-6 h-0.5 bg-gradient-to-r from-blue-400 to-indigo-400 rounded-full"></div>
                </h4>
                <ul class="space-y-4">
                    <li>
                        <a href="{{ route('login') }}" class="text-gray-300 hover:text-white transition-all duration-300 flex items-center group">
                            <svg class="w-4 h-4 mr-3 text-blue-400 group-hover:text-white transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
                            </svg>
                            Login
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('register') }}" class="text-gray-300 hover:text-white transition-all duration-300 flex items-center group">
                            <svg class="w-4 h-4 mr-3 text-blue-400 group-hover:text-white transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                            </svg>
                            Register
                        </a>
                    </li>
                </ul>
            </div>

            <!-- Contact & Support -->
            <div class="lg:col-span-1">
                <h4 class="text-lg font-semibold text-white mb-6 relative">
                    Contact & Support
                    <div class="absolute bottom-0 left-0 w-6 h-0.5 bg-gradient-to-r from-blue-400 to-indigo-400 rounded-full"></div>
                </h4>

                <div class="space-y-6">
                    <!-- Contact Info -->
                    <div class="flex items-start text-gray-300">
                        <svg class="h-6 w-6 mr-4 text-blue-400 mt-1 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                        </svg>
                        <div>
                            <div class="font-semibold text-white mb-1">Email Support</div>
                            <div class="text-sm leading-relaxed">itso@nu-laguna.edu.ph</div>
                        </div>
                    </div>

                    <div class="flex items-start text-gray-300">
                        <svg class="h-6 w-6 mr-4 text-blue-400 mt-1 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                        <div>
                            <div class="font-semibold text-white mb-1">Location</div>
                            <div class="text-sm leading-relaxed">KM. 53, Pan Philippine Highway<br>Brgy. Milagrosa, Calamba City<br>Laguna, Philippines 4027</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Bottom Section -->
        <div class="mt-16 pt-8 border-t border-white/10">
            <div class="flex flex-col lg:flex-row justify-between items-center">
                <div class="flex flex-col lg:flex-row items-center space-y-4 lg:space-y-0 lg:space-x-6 mb-6 lg:mb-0">
                    <p class="text-gray-400 text-sm">
                        &copy; {{ date('Y') }} AssetLab. All rights reserved. | National University - Laguna
                    </p>
                </div>

                <div class="flex items-center space-x-4">
                    <span class="text-gray-400 text-sm">Powered by</span>
                    <div class="flex items-center space-x-2">
                        <div class="w-8 h-8 bg-gradient-to-br from-blue-400 to-indigo-500 rounded-lg flex items-center justify-center">
                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                            </svg>
                        </div>
                        <span class="text-white font-semibold">Laravel</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</footer> 