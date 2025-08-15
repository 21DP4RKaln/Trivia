// Game Card Functionality
class GameCardManager {
    constructor() {
        this.modal = null;
        this.modalContent = null;
        this.template = null;
        this.init();
    }

    init() {
        this.modal = document.getElementById('gameModal');
        this.modalContent = document.getElementById('modalContent');
        this.template = document.getElementById('gameModalTemplate');
        
        // Setup modal close on escape key
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && this.modal.classList.contains('show')) {
                this.closeModal();
            }
        });

        // Animate cards on load
        this.animateCards();
    }

    async openModal(gameId) {
        if (!this.modal) return;

        // Show modal with loading state
        this.modal.classList.add('show');
        document.body.classList.add('modal-open');
        
        // Show loading spinner
        this.modalContent.innerHTML = `
            <div class="loading-spinner">
                <i class="fas fa-spinner fa-spin"></i>
                Loading game details...
            </div>
        `;

        try {
            // Fetch game details
            const response = await fetch(`/admin/game-details/${gameId}`, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                }
            });

            if (!response.ok) {
                throw new Error('Failed to fetch game details');
            }

            const gameData = await response.json();
            this.populateModal(gameData);

        } catch (error) {
            console.error('Error loading game details:', error);
            this.modalContent.innerHTML = `
                <div class="error-message">
                    <i class="fas fa-exclamation-triangle"></i>
                    <h3>Error Loading Game Details</h3>
                    <p>Sorry, we couldn't load the game details. Please try again.</p>
                    <button class="btn btn-primary" onclick="gameCardManager.closeModal()">Close</button>
                </div>
            `;
        }
    }

    populateModal(game) {
        if (!this.template) return;

        // Clone the template
        const content = this.template.content.cloneNode(true);

        // Populate player information
        const avatarText = content.querySelector('.avatar-text');
        const playerName = content.querySelector('.player-name-modal');
        const playerEmail = content.querySelector('.player-email-modal');
        const gamesCount = content.querySelector('.games-count');
        const accuracyAvg = content.querySelector('.accuracy-avg');

        if (avatarText) avatarText.textContent = (game.user?.name || 'G').charAt(0).toUpperCase();
        if (playerName) playerName.textContent = game.user?.name || 'Guest Player';
        if (playerEmail) playerEmail.textContent = game.user?.email || 'No email';
        if (gamesCount) gamesCount.textContent = game.user?.total_games || '1';
        if (accuracyAvg) accuracyAvg.textContent = `${(game.user?.average_accuracy || game.accuracy).toFixed(1)}%`;

        // Populate game details
        const correctAnswers = content.querySelector('.correct-answers');
        const scorePercentage = content.querySelector('.score-percentage');
        const accuracyValue = content.querySelector('.accuracy-value');
        const accuracyFill = content.querySelector('.accuracy-fill-modal');
        const durationValue = content.querySelector('.duration-value');
        const avgQuestionTime = content.querySelector('.avg-question-time');
        const gameDate = content.querySelector('.game-date');
        const gameTime = content.querySelector('.game-time');
        const relativeTime = content.querySelector('.relative-time');

        if (correctAnswers) correctAnswers.textContent = game.correct_answers;
        if (scorePercentage) scorePercentage.textContent = `${((game.correct_answers / 20) * 100).toFixed(1)}%`;
        if (accuracyValue) accuracyValue.textContent = game.accuracy.toFixed(1);
        if (accuracyFill) accuracyFill.style.width = `${game.accuracy}%`;
        if (durationValue) durationValue.textContent = this.formatDuration(game.duration_seconds);
        if (avgQuestionTime) avgQuestionTime.textContent = `${(game.duration_seconds / 20).toFixed(1)}s`;
        if (gameDate) gameDate.textContent = this.formatDate(game.created_at);
        if (gameTime) gameTime.textContent = this.formatTime(game.created_at);
        if (relativeTime) relativeTime.textContent = this.getRelativeTime(game.created_at);

        // Handle question timing if available
        if (game.question_times && game.question_times.length > 0) {
            this.populateQuestionTiming(content, game.question_times);
        }

        // Add badge based on score
        this.addScoreBadge(content, game.correct_answers);

        // Replace modal content
        this.modalContent.innerHTML = '';
        this.modalContent.appendChild(content);

        // Animate modal content
        this.animateModalContent();
    }

    populateQuestionTiming(content, questionTimes) {
        const timingSection = content.querySelector('.question-timing-section');
        if (!timingSection) return;

        timingSection.style.display = 'block';

        const times = questionTimes.map(qt => qt.duration);
        const fastest = Math.min(...times);
        const slowest = Math.max(...times);
        const average = times.reduce((sum, time) => sum + time, 0) / times.length;

        const fastestTime = content.querySelector('.fastest-time');
        const slowestTime = content.querySelector('.slowest-time');
        const averageTime = content.querySelector('.average-time');

        if (fastestTime) fastestTime.textContent = `${fastest.toFixed(1)}s`;
        if (slowestTime) slowestTime.textContent = `${slowest.toFixed(1)}s`;
        if (averageTime) averageTime.textContent = `${average.toFixed(1)}s`;
    }

    addScoreBadge(content, score) {
        const scoreCard = content.querySelector('.score-card');
        if (!scoreCard) return;

        let badgeClass = '';
        let badgeText = '';
        let badgeIcon = '';

        if (score === 20) {
            badgeClass = 'perfect-badge';
            badgeText = 'Perfect!';
            badgeIcon = 'fas fa-crown';
        } else if (score >= 18) {
            badgeClass = 'excellent-badge';
            badgeText = 'Excellent';
            badgeIcon = 'fas fa-star';
        } else if (score >= 15) {
            badgeClass = 'good-badge';
            badgeText = 'Good';
            badgeIcon = 'fas fa-thumbs-up';
        } else if (score >= 12) {
            badgeClass = 'average-badge';
            badgeText = 'Average';
            badgeIcon = 'fas fa-minus';
        } else {
            badgeClass = 'poor-badge';
            badgeText = 'Needs Improvement';
            badgeIcon = 'fas fa-thumbs-down';
        }

        const badge = document.createElement('div');
        badge.className = `score-badge ${badgeClass}`;
        badge.innerHTML = `<i class="${badgeIcon}"></i> ${badgeText}`;
        
        const cardContent = scoreCard.querySelector('.card-content');
        if (cardContent) {
            cardContent.appendChild(badge);
        }
    }

    closeModal() {
        if (!this.modal) return;

        this.modal.classList.remove('show');
        document.body.classList.remove('modal-open');
        
        // Clear content after animation
        setTimeout(() => {
            if (this.modalContent) {
                this.modalContent.innerHTML = `
                    <div class="loading-spinner">
                        <i class="fas fa-spinner fa-spin"></i>
                        Loading game details...
                    </div>
                `;
            }
        }, 300);
    }

    animateCards() {
        const cards = document.querySelectorAll('.game-card');
        cards.forEach((card, index) => {
            card.style.opacity = '0';
            card.style.transform = 'translateY(20px) scale(0.95)';
            
            setTimeout(() => {
                card.style.transition = 'all 0.5s cubic-bezier(0.4, 0, 0.2, 1)';
                card.style.opacity = '1';
                card.style.transform = 'translateY(0) scale(1)';
            }, index * 100);
        });
    }

    animateModalContent() {
        const elements = this.modalContent.querySelectorAll('.detail-card');
        elements.forEach((el, index) => {
            el.style.opacity = '0';
            el.style.transform = 'translateY(20px)';
            
            setTimeout(() => {
                el.style.transition = 'all 0.4s cubic-bezier(0.4, 0, 0.2, 1)';
                el.style.opacity = '1';
                el.style.transform = 'translateY(0)';
            }, index * 100);
        });
    }

    formatDuration(seconds) {
        const minutes = Math.floor(seconds / 60);
        const remainingSeconds = seconds % 60;
        return `${minutes}:${remainingSeconds.toString().padStart(2, '0')}`;
    }

    formatDate(dateString) {
        const date = new Date(dateString);
        return date.toLocaleDateString('en-US', { 
            year: 'numeric', 
            month: 'short', 
            day: 'numeric' 
        });
    }

    formatTime(dateString) {
        const date = new Date(dateString);
        return date.toLocaleTimeString('en-US', { 
            hour: '2-digit', 
            minute: '2-digit' 
        });
    }

    getRelativeTime(dateString) {
        const date = new Date(dateString);
        const now = new Date();
        const diffMs = now - date;
        const diffHours = Math.floor(diffMs / (1000 * 60 * 60));
        const diffDays = Math.floor(diffHours / 24);

        if (diffDays > 0) {
            return `${diffDays} day${diffDays > 1 ? 's' : ''} ago`;
        } else if (diffHours > 0) {
            return `${diffHours} hour${diffHours > 1 ? 's' : ''} ago`;
        } else {
            const diffMinutes = Math.floor(diffMs / (1000 * 60));
            return `${Math.max(1, diffMinutes)} minute${diffMinutes > 1 ? 's' : ''} ago`;
        }
    }
}

// Global functions for onclick handlers
let gameCardManager;

function openGameModal(gameId) {
    if (gameCardManager) {
        gameCardManager.openModal(gameId);
    }
}

function closeGameModal() {
    if (gameCardManager) {
        gameCardManager.closeModal();
    }
}

// Initialize when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    gameCardManager = new GameCardManager();
});
