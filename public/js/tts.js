class TextToSpeech {
    constructor() {
        this.synth = window.speechSynthesis;
        this.voices = [];
        this.selectedVoice = null;
        this.isPlaying = false;
    }

    // Initialize available voices
    async init() {
        // Wait for voices to load
        this.voices = await this.getVoices();
        // Select Arabic voice if available, otherwise use first available voice
        // this.selectedVoice = this.voices.find(voice => voice.lang.includes('ar')) || this.voices[0];
    }

    // Get available voices
    getVoices() {
        return new Promise((resolve) => {
            let voices = this.synth.getVoices();
            if (voices.length > 0) {
                resolve(voices);
            } else {
                this.synth.onvoiceschanged = () => {
                    voices = this.synth.getVoices();
                    resolve(voices);
                };
            }
        });
    }

    // Speak the text
    speak(text) {
        if (this.isPlaying) {
            this.stop();
        }

        const utterance = new SpeechSynthesisUtterance(text);
        utterance.voice = this.selectedVoice;
        utterance.rate = 1.0; // Speech rate
        utterance.pitch = 1.0; // Pitch level
        utterance.volume = 1.0; // Volume level

        utterance.onstart = () => {
            this.isPlaying = true;
            this.updateButtonState(true);
        };

        utterance.onend = () => {
            this.isPlaying = false;
            this.updateButtonState(false);
        };

        this.synth.speak(utterance);
    }

    // Stop playback
    stop() {
        this.synth.cancel();
        this.isPlaying = false;
        this.updateButtonState(false);
    }

    // Update button state
    updateButtonState(isPlaying) {
        const button = document.getElementById('ttsButton');
        if (button) {
            button.innerHTML = isPlaying ? 
                '<i class="fas fa-stop"></i> Stop' : 
                '<i class="fas fa-volume-up"></i> Listen to Description';
        }
    }
}

