/*
 * SCREENSHOT CAPTURE MODULE
 *
 * This module contains the real screen capture functionality.
 * Currently DISABLED - only the fake alert system is active.
 *
 * To enable real screen capture:
 * 1. Uncomment the code below
 * 2. Include this file in your HTML
 * 3. Call screenshotCapture.startCapturing() in startSession()
 * 4. Set up the proper Laravel API endpoint for screenshot uploads
 */

/*
class AutoScreenshot {
    constructor(apiEndpoint, interval = 3000) {
        this.apiEndpoint = apiEndpoint;
        this.interval = interval;
        this.isCapturing = false;
        this.intervalId = null;
        this.mediaStream = null;
        this.video = null;
    }

    async initializeCapture() {
        try {
            // Force ENTIRE SCREEN sharing only
            this.mediaStream = await navigator.mediaDevices.getDisplayMedia({
                video: {
                    mediaSource: 'screen',
                    displaySurface: 'monitor' // Force entire screen
                },
                audio: false,
                preferCurrentTab: false // Don't allow current tab selection
            });

            // Check if user actually shared the entire screen
            const videoTrack = this.mediaStream.getVideoTracks()[0];
            const settings = videoTrack.getSettings();

            console.log('Capture settings:', settings);

            // If it's not the entire screen, reject it
            if (settings.displaySurface !== 'monitor') {
                // Stop the stream
                this.mediaStream.getTracks().forEach(track => track.stop());

                alert('You must share your ENTIRE SCREEN, not just a window or tab. Please try again and select "Entire Screen".');
                return false;
            }

            // Create video element for capturing frames
            this.video = document.createElement('video');
            this.video.srcObject = this.mediaStream;
            this.video.play();

            // Handle stream ending (user stops sharing)
            videoTrack.addEventListener('ended', () => {
                this.stopCapturing();
                console.log('Capture stopped - user ended screen sharing');
            });

            console.log('Full screen capture initialized successfully');
            return true;
        } catch (error) {
            console.error('Error initializing capture:', error);

            if (error.name === 'NotAllowedError') {
                alert('Screen sharing permission denied. You must allow screen sharing to continue.');
            } else if (error.name === 'NotSupportedError') {
                alert('Screen sharing not supported in this browser.');
            } else {
                alert('Failed to initialize screen capture. Please try again.');
            }

            return false;
        }
    }

    captureFrame() {
        if (!this.video || this.video.readyState !== 4) {
            console.log('Video not ready');
            return null;
        }

        const canvas = document.createElement('canvas');

        // Compress heavily - reduce resolution
        const scale = 0.2; // 20% of original size for maximum compression
        canvas.width = this.video.videoWidth * scale;
        canvas.height = this.video.videoHeight * scale;

        const ctx = canvas.getContext('2d');

        // Smooth scaling for better compression
        ctx.imageSmoothingEnabled = true;
        ctx.imageSmoothingQuality = 'low';

        // Draw scaled down image
        ctx.drawImage(this.video, 0, 0, canvas.width, canvas.height);

        return new Promise((resolve) => {
            // Maximum compression: quality = 0.05 (5% quality)
            canvas.toBlob(resolve, 'image/jpeg', 0.05);
        });
    }

    async sendScreenshot(blob) {
        if (!blob) {
            console.log('No blob to upload');
            return false;
        }

        console.log('Preparing to upload blob:', blob.size, 'bytes');

        const formData = new FormData();
        formData.append('screenshot', blob, `screenshot_${Date.now()}.jpg`);
        formData.append('timestamp', new Date().toISOString());
        formData.append('interview_slug', interviewSlug); // Make sure this variable is available

        try {
            // Get CSRF token safely
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

            const headers = {};
            if (csrfToken) {
                headers['X-CSRF-TOKEN'] = csrfToken;
            }

            console.log('Sending request to:', this.apiEndpoint);
            console.log('CSRF token:', csrfToken ? 'present' : 'missing');

            const response = await fetch(this.apiEndpoint, {
                method: 'POST',
                body: formData,
                headers: headers
            });

            console.log('Response status:', response.status);

            if (!response.ok) {
                const errorText = await response.text();
                console.error('Server response:', errorText);
                throw new Error(`HTTP error! status: ${response.status} - ${errorText}`);
            }

            const result = await response.json();
            console.log('Screenshot uploaded successfully:', result);
            return true;
        } catch (error) {
            console.error('Error uploading screenshot:', error);
            return false;
        }
    }

    async startCapturing() {
        if (this.isCapturing) return false;

        console.log('Requesting screen access...');

        // Try to initialize capture (get permission)
        const initialized = await this.initializeCapture();
        if (!initialized) {
            console.log('Failed - You must share your ENTIRE SCREEN');
            return false;
        }

        this.isCapturing = true;
        console.log('Full screen capture active');

        // Wait for video to be ready
        await new Promise(resolve => {
            if (this.video.readyState === 4) {
                resolve();
            } else {
                this.video.onloadeddata = resolve;
            }
        });

        this.captureLoop();
        return true;
    }

    async captureLoop() {
        if (!this.isCapturing) return;

        const screenshot = await this.captureFrame();
        if (screenshot) {
            await this.sendScreenshot(screenshot);
        }

        if (this.isCapturing) {
            // Random interval between 1-5 seconds
            const randomDelay = (Math.random() * 4 + 1) * 1000;
            this.intervalId = setTimeout(() => this.captureLoop(), randomDelay);
        }
    }

    stopCapturing() {
        if (!this.isCapturing) return;

        this.isCapturing = false;

        if (this.intervalId) {
            clearTimeout(this.intervalId);
            this.intervalId = null;
        }

        if (this.mediaStream) {
            this.mediaStream.getTracks().forEach(track => track.stop());
            this.mediaStream = null;
        }

        if (this.video) {
            this.video.srcObject = null;
            this.video = null;
        }

        console.log('Screenshot capture stopped');
    }
}

// Usage (when you want to enable real screen capture):
// let screenshotCapture;
// screenshotCapture = new AutoScreenshot('/api/session/upload-screenshot');
// const screenCaptureStarted = await screenshotCapture.startCapturing();
*/

// For now, just export empty functions to avoid errors
window.AutoScreenshot = class {
    constructor() {}
    async startCapturing() { return true; }
    stopCapturing() {}
};
