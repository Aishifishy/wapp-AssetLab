<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'AssetLab') }} - @yield('title')</title>

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <!-- Enhanced Password Field Styles -->
    <style>
        .password-field-wrapper {
            position: relative;
            display: block;
            width: 100%;
            isolation: isolate;
            height: auto;
        }
        
        .password-field-wrapper input {
            display: block;
            width: 100%;
        }
        
        .password-field-wrapper input {
            padding-right: 2.5rem !important;
            position: relative;
            z-index: 1;
            height: auto;
            min-height: 2.5rem;
        }
        
        .password-toggle {
            position: absolute;
            right: 1px;
            top: 1px;
            height: calc(100% - 2px);
            width: 2.5rem;
            display: flex;
            align-items: center;
            justify-content: center;
            background: transparent;
            border: none;
            cursor: pointer;
            border-radius: 0 calc(0.375rem - 1px) calc(0.375rem - 1px) 0;
            transition: all 0.2s ease-in-out;
            z-index: 10;
        }
        
        .password-toggle:hover {
            background-color: rgba(59, 130, 246, 0.05);
        }
        
        .password-toggle:focus {
            outline: none;
            background-color: rgba(59, 130, 246, 0.1);
            box-shadow: inset 0 0 0 2px rgba(59, 130, 246, 0.2);
        }
        
        .password-toggle:active {
            background-color: rgba(59, 130, 246, 0.15);
            transform: scale(0.95);
        }
        
        .password-toggle svg {
            transition: all 0.2s ease-in-out;
        }
        
        .password-toggle:hover svg {
            color: #3b82f6;
        }
        
        /* Enhanced focus styles for better accessibility */
        .password-field-wrapper input:focus + .password-toggle {
            border-left: 1px solid #3b82f6;
        }
        
        /* Ensure toggle button positioning is independent of error messages */
        .password-field-wrapper {
            contain: layout;
        }
        
        .password-field-wrapper::after {
            content: '';
            display: block;
            clear: both;
        }
        
        /* Smooth icon transitions */
        .password-toggle svg {
            opacity: 1;
            transform: scale(1);
        }
        
        .password-toggle svg.hidden {
            opacity: 0;
            transform: scale(0.8);
        }
        
        /* Form error positioning for password fields */
        .password-field-container {
            width: 100%;
            display: block;
        }
        
        .password-field-container > * {
            display: block;
            width: 100%;
        }
        
        .password-field-container .password-field-wrapper {
            display: block;
            position: relative;
        }
        
        /* Force error messages to appear beneath password fields */
        .password-field-container p {
            display: block !important;
            width: 100% !important;
            margin-top: 0.5rem !important;
            clear: both !important;
            float: none !important;
            position: static !important;
        }
        
        /* Additional specificity for error messages */
        .password-field-container [class*="text-red"],
        .password-field-container .text-red-600 {
            display: block !important;
            width: 100% !important;
            margin-top: 0.5rem !important;
            clear: both !important;
            float: none !important;
            position: static !important;
            box-sizing: border-box !important;
        }
        
        /* Error message container */
        .error-message-container {
            display: block;
            width: 100%;
            clear: both;
            position: relative;
            z-index: 0;
        }
        
        .error-message-container p {
            display: block !important;
            width: 100% !important;
            margin-top: 0.5rem !important;
            clear: both !important;
            float: none !important;
            position: static !important;
        }
        
        /* Prevent error container from affecting input field positioning */
        .password-field-container > .password-field-wrapper {
            position: relative;
            z-index: 2;
        }
        
        .password-field-container > .error-message-container {
            position: relative;
            z-index: 1;
        }
        
        /* Mobile optimizations */
        @media (max-width: 640px) {
            .password-toggle {
                width: 3rem;
            }
            
            .password-field-wrapper input {
                padding-right: 3rem !important;
            }
        }
    </style>
</head>
<body class="font-sans antialiased">
    <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gray-100">
        <div class="w-full sm:max-w-md mt-6 px-6 py-4 bg-white shadow-md overflow-hidden sm:rounded-lg">
            <div class="mb-6 text-center">
                <h1 class="text-2xl font-bold text-gray-900">
                    <a href="/">AssetLab</a>
                </h1>
                <h2 class="mt-2 text-gray-600">@yield('subtitle')</h2>
            </div>

            @yield('content')
        </div>
    </div>

    <!-- Enhanced Password Toggle Script -->
    <script>
        function togglePassword(fieldId) {
            const passwordField = document.getElementById(fieldId);
            const showIcon = document.getElementById(fieldId + '-show-icon');
            const hideIcon = document.getElementById(fieldId + '-hide-icon');
            
            if (passwordField && showIcon && hideIcon) {
                const isPassword = passwordField.type === 'password';
                
                // Toggle input type
                passwordField.type = isPassword ? 'text' : 'password';
                
                // Smooth icon transition
                if (isPassword) {
                    // Showing password
                    showIcon.style.opacity = '0';
                    showIcon.style.transform = 'scale(0.8)';
                    
                    setTimeout(() => {
                        showIcon.classList.add('hidden');
                        hideIcon.classList.remove('hidden');
                        hideIcon.style.opacity = '1';
                        hideIcon.style.transform = 'scale(1)';
                    }, 100);
                } else {
                    // Hiding password
                    hideIcon.style.opacity = '0';
                    hideIcon.style.transform = 'scale(0.8)';
                    
                    setTimeout(() => {
                        hideIcon.classList.add('hidden');
                        showIcon.classList.remove('hidden');
                        showIcon.style.opacity = '1';
                        showIcon.style.transform = 'scale(1)';
                    }, 100);
                }
                
                // Update aria-label for better accessibility
                const button = showIcon.closest('.password-toggle') || hideIcon.closest('.password-toggle');
                if (button) {
                    button.setAttribute('aria-label', isPassword ? 'Hide password' : 'Show password');
                }
            }
        }

        // Enhanced initialization
        document.addEventListener('DOMContentLoaded', function() {
            const toggleButtons = document.querySelectorAll('.password-toggle');
            
            // Function to adjust button height to match input
            function adjustButtonHeight(button) {
                const wrapper = button.closest('.password-field-wrapper');
                const input = wrapper ? wrapper.querySelector('input') : null;
                
                if (input) {
                    const inputHeight = input.offsetHeight;
                    button.style.height = (inputHeight - 2) + 'px'; // Account for borders
                }
            }
            
            toggleButtons.forEach((button, index) => {
                // Set initial button height
                adjustButtonHeight(button);
                
                // Enhanced keyboard support
                button.addEventListener('keydown', function(e) {
                    if (e.key === 'Enter' || e.key === ' ') {
                        e.preventDefault();
                        button.click();
                    }
                });
                
                // Improved accessibility
                button.setAttribute('tabindex', '0');
                button.setAttribute('role', 'button');
                button.setAttribute('aria-label', 'Show password');
                
                // Enhanced visual feedback
                button.addEventListener('mousedown', function(e) {
                    e.preventDefault(); // Prevent input blur
                    button.style.transform = 'scale(0.95)';
                });
                
                button.addEventListener('mouseup', function() {
                    button.style.transform = 'scale(1)';
                });
                
                button.addEventListener('mouseleave', function() {
                    button.style.transform = 'scale(1)';
                });
                
                // Prevent form submission on button click
                button.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                });
            });
            
            // Adjust button heights on window resize
            window.addEventListener('resize', function() {
                toggleButtons.forEach(adjustButtonHeight);
            });
            
            // Re-adjust heights when errors appear/disappear (using MutationObserver)
            const observers = [];
            toggleButtons.forEach(button => {
                const container = button.closest('.password-field-container');
                if (container) {
                    const observer = new MutationObserver(function() {
                        setTimeout(() => adjustButtonHeight(button), 10);
                    });
                    
                    observer.observe(container, {
                        childList: true,
                        subtree: true,
                        attributes: true,
                        attributeFilter: ['class', 'style']
                    });
                    
                    observers.push(observer);
                }
            });
            
            // Smooth transitions for all icons
            const allIcons = document.querySelectorAll('.password-toggle svg');
            allIcons.forEach(icon => {
                icon.style.transition = 'all 0.2s ease-in-out';
            });
        });
    </script>
</body>
</html> 