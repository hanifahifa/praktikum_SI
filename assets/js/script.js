// ================================
// MULTI-STEP FORM NAVIGATION
// ================================
let currentStep = 1;

// Emoji mapping berdasarkan nilai slider
const emojiMap = {
    1: '😞',
    2: '😕',
    3: '😐',
    4: '🙂',
    5: '😍'
};

// ================================
// UPDATE SLIDER VALUE & EMOJI
// ================================
function updateSlider(sliderId) {
    const slider = document.getElementById(sliderId);
    const valueDisplay = document.getElementById('value_' + sliderId);
    const emojiDisplay = document.getElementById('emoji_' + sliderId);
    
    if (!slider || !valueDisplay || !emojiDisplay) {
        console.error('Element not found:', sliderId);
        return;
    }
    
    const value = parseInt(slider.value);
    
    // Update badge nilai
    valueDisplay.textContent = value;
    
    // Update emoji
    emojiDisplay.textContent = emojiMap[value];
    
    // Animasi emoji yang smooth
    emojiDisplay.style.transform = 'scale(1.15)';
    setTimeout(() => {
        emojiDisplay.style.transform = 'scale(1)';
    }, 150);
}

// ================================
// NEXT STEP
// ================================
function nextStep(stepNumber) {
    // Hide current step
    const currentStepEl = document.getElementById('step' + currentStep);
    if (currentStepEl) {
        currentStepEl.classList.remove('active');
    }
    
    // Show next step
    const nextStepEl = document.getElementById('step' + stepNumber);
    if (nextStepEl) {
        nextStepEl.classList.add('active');
    }
    
    // Update step counter
    currentStep = stepNumber;
    
    // Update progress bar
    updateProgress();
    
    // Scroll to top smoothly
    window.scrollTo({ top: 0, behavior: 'smooth' });
}

// ================================
// PREVIOUS STEP
// ================================
function prevStep(stepNumber) {
    // Hide current step
    const currentStepEl = document.getElementById('step' + currentStep);
    if (currentStepEl) {
        currentStepEl.classList.remove('active');
    }
    
    // Show previous step
    const prevStepEl = document.getElementById('step' + stepNumber);
    if (prevStepEl) {
        prevStepEl.classList.add('active');
    }
    
    // Update step counter
    currentStep = stepNumber;
    
    // Update progress bar
    updateProgress();
    
    // Scroll to top smoothly
    window.scrollTo({ top: 0, behavior: 'smooth' });
}

// ================================
// UPDATE PROGRESS BAR
// ================================
function updateProgress() {
    const progressBar = document.getElementById('progressBar');
    const progressText = document.getElementById('progressText');
    
    if (!progressBar || !progressText) {
        console.error('Progress elements not found');
        return;
    }
    
    // Calculate percentage (33.33%, 66.66%, 100%)
    const percentage = (currentStep / 3) * 100;
    
    // Update progress bar width
    progressBar.style.width = percentage + '%';
    
    // Update text
    progressText.textContent = `Bagian ${currentStep} dari 3`;
}

// ================================
// INITIALIZE ON PAGE LOAD
// ================================
document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM loaded, initializing sliders...');
    
    // Initialize all sliders
    const sliders = document.querySelectorAll('.form-range');
    
    if (sliders.length === 0) {
        console.error('No sliders found!');
    } else {
        console.log('Found', sliders.length, 'sliders');
    }
    
    sliders.forEach(slider => {
        const sliderId = slider.id;
        console.log('Initializing slider:', sliderId);
        
        // Set initial emoji & value
        updateSlider(sliderId);
        
        // Add event listener for real-time updates
        slider.addEventListener('input', function() {
            updateSlider(sliderId);
        });
    });
    
    // Form submit handler (optional validation)
    const form = document.getElementById('mainForm');
    if (form) {
        form.addEventListener('submit', function(e) {
            console.log('Form submitted!');
            // Form akan otomatis submit ke hasil.php
        });
    }
});

// ================================
// CSS TRANSITION FOR EMOJI
// ================================
// Add inline style for smooth emoji animation
const style = document.createElement('style');
style.textContent = `
    .text-center.fs-1 {
        transition: transform 0.2s ease;
    }
`;
document.head.appendChild(style);