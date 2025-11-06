// /**
//  * Typing & Deleting Loop Animation
//  * Text appears character by character, then deletes character by character, then repeats
//  */

// class TypingDeleteLoop {
//     constructor(config = {}) {
//         this.elementId = config.elementId;
//         this.texts = config.texts || [];
//         this.typingSpeed = config.typingSpeed || 100;      // ms per character when typing
//         this.deletingSpeed = config.deletingSpeed || 50;   // ms per character when deleting
//         this.pauseBeforeDelete = config.pauseBeforeDelete || 1500;  // pause after typing before deleting
//         this.pauseBeforeNext = config.pauseBeforeNext || 500;       // pause after deleting before next text

//         this.element = document.getElementById(this.elementId);
//         this.currentTextIndex = 0;
//         this.currentCharIndex = 0;
//         this.isDeleting = false;
//         this.isTyping = false;
//     }

//     /**
//      * Start the loop animation
//      */
//     start() {
//         this.type();
//     }

//     /**
//      * Main typing/deleting loop logic
//      */
//     type() {
//         // Get current text
//         const currentText = this.texts[this.currentTextIndex];

//         // Typing phase
//         if (!this.isDeleting) {
//             // Add one character
//             this.currentCharIndex++;
//             this.element.textContent = currentText.substring(0, this.currentCharIndex);

//             // Check if we finished typing
//             if (this.currentCharIndex === currentText.length) {
//                 // Finished typing - pause before deleting
//                 setTimeout(() => {
//                     this.isDeleting = true;
//                     this.type();
//                 }, this.pauseBeforeDelete);
//                 return;
//             }

//             // Schedule next character
//             setTimeout(() => this.type(), this.typingSpeed);
//         }
//         // Deleting phase
//         else {
//             // Remove one character
//             this.currentCharIndex--;
//             this.element.textContent = currentText.substring(0, this.currentCharIndex);

//             // Check if we finished deleting
//             if (this.currentCharIndex === 0) {
//                 // Finished deleting - move to next text
//                 this.isDeleting = false;
//                 this.currentTextIndex = (this.currentTextIndex + 1) % this.texts.length;

//                 // Pause before typing next text
//                 setTimeout(() => this.type(), this.pauseBeforeNext);
//                 return;
//             }

//             // Schedule next deletion
//             setTimeout(() => this.type(), this.deletingSpeed);
//         }
//     }
// }

// // Initialize when page loads
// document.addEventListener('DOMContentLoaded', function() {
//     const config = window.typingDeleteLoopConfig;
//     if (config) {
//         const animator = new TypingDeleteLoop(config);
//         animator.start();
//     }
// });



// // ## How It Works

// // ### Step-by-step execution:
// // ```
// // 1. W
// // 2. We
// // 3. Wel
// // 4. Welc
// // 5. Welco
// // 6. Welcom
// // 7. Welcome
// // (pause 1500ms)
// // 8. Welcom
// // 9. Welco
// // 10. Welc
// // 11. Wel
// // 12. We
// // 13. W
// // 14. (empty)
// // (pause 500ms)
// // 15. Start typing next text...
