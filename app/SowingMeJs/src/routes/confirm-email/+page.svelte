<script>
  import { onMount } from 'svelte';
  import { page } from '$app/stores';
  import { userData } from '../../lib/stores.js';

  let confirming = true;
  let confirmed = false;
  let errorMessage = '';
  let showModal = false;

  onMount(async () => {
    const token = $page.url.searchParams.get('token');

    if (!token) {
      confirming = false;
      errorMessage = 'No confirmation token provided';
      return;
    }

    try {
      const response = await fetch($userData.apiBaseUrl + '/confirm-email?token=' + token, {
        method: 'GET',
        credentials: 'include',
      });

      const data = await response.json();

      if (response.ok) {
        confirmed = true;
        confirming = false;

        // Update user data store with the confirmed user
        if (data.user) {
          userData.update(current => ({
            ...current,
            user: data.user
          }));
        }

        // Show the congratulations modal
        showModal = true;
      } else {
        confirming = false;
        errorMessage = data.message || 'Email confirmation failed';
      }
    } catch (error) {
      confirming = false;
      errorMessage = 'An error occurred during confirmation. Please try again.';
      console.error('Confirmation error:', error);
    }
  });

  function closeModal() {
    showModal = false;
    window.location.href = '/';
  }
</script>

{#if confirming}
  <div class="confirmation-container">
    <div class="spinner"></div>
    <h2>Confirming your email...</h2>
    <p>Please wait while we verify your account.</p>
  </div>
{:else if confirmed && showModal}
  <div class="modal-overlay" on:click={closeModal} role="button" tabindex="0">
    <div class="modal-card" on:click|stopPropagation role="dialog" tabindex="-1">
      <div class="modal-header">
        <h1>Congratulations!</h1>
      </div>
      <div class="modal-content">
        <div class="success-icon">✓</div>
        <p class="success-message">Your account is now confirmed.</p>
        <p class="welcome-message">You're now able to start planting and watch other ministries grow.</p>
      </div>
      <div class="modal-footer">
        <button class="btn-primary" on:click={closeModal}>Get Started</button>
      </div>
    </div>
  </div>
{:else if errorMessage}
  <div class="error-container">
    <div class="error-icon">✕</div>
    <h2>Confirmation Failed</h2>
    <p class="error-message">{errorMessage}</p>
    <a href="/" class="btn-secondary">Return to Home</a>
  </div>
{/if}

<style>
  .confirmation-container,
  .error-container {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    min-height: 60vh;
    padding: 2rem;
    text-align: center;
  }

  .spinner {
    width: 50px;
    height: 50px;
    border: 4px solid #f3f3f3;
    border-top: 4px solid #4A90E2;
    border-radius: 50%;
    animation: spin 1s linear infinite;
    margin-bottom: 1.5rem;
  }

  @keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
  }

  .modal-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.6);
    backdrop-filter: blur(8px);
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 1000;
  }

  .modal-card {
    background: white;
    border-radius: 12px;
    box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
    max-width: 500px;
    width: 90%;
    overflow: hidden;
    animation: slideIn 0.3s ease-out;
  }

  @keyframes slideIn {
    from {
      transform: translateY(-50px);
      opacity: 0;
    }
    to {
      transform: translateY(0);
      opacity: 1;
    }
  }

  .modal-header {
    background: linear-gradient(135deg, #4A90E2 0%, #357ABD 100%);
    color: white;
    padding: 2rem;
    text-align: center;
  }

  .modal-header h1 {
    margin: 0;
    font-size: 2rem;
    font-weight: 600;
  }

  .modal-content {
    padding: 2.5rem;
    text-align: center;
  }

  .success-icon {
    width: 80px;
    height: 80px;
    background: #4CAF50;
    color: white;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 3rem;
    font-weight: bold;
    margin: 0 auto 1.5rem;
    animation: scaleIn 0.5s ease-out 0.2s both;
  }

  @keyframes scaleIn {
    from {
      transform: scale(0);
    }
    to {
      transform: scale(1);
    }
  }

  .success-message {
    font-size: 1.25rem;
    font-weight: 600;
    color: #333;
    margin-bottom: 1rem;
  }

  .welcome-message {
    font-size: 1rem;
    color: #666;
    line-height: 1.6;
  }

  .modal-footer {
    padding: 0 2.5rem 2.5rem;
    text-align: center;
  }

  .btn-primary {
    background: #4A90E2;
    color: white;
    border: none;
    padding: 0.875rem 2.5rem;
    font-size: 1rem;
    font-weight: 600;
    border-radius: 6px;
    cursor: pointer;
    transition: all 0.2s ease;
    width: 100%;
  }

  .btn-primary:hover {
    background: #357ABD;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(74, 144, 226, 0.4);
  }

  .error-icon {
    width: 80px;
    height: 80px;
    background: #f44336;
    color: white;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 3rem;
    font-weight: bold;
    margin: 0 auto 1.5rem;
  }

  .error-message {
    color: #666;
    font-size: 1rem;
    margin-bottom: 2rem;
  }

  .btn-secondary {
    background: #757575;
    color: white;
    text-decoration: none;
    padding: 0.875rem 2.5rem;
    font-size: 1rem;
    font-weight: 600;
    border-radius: 6px;
    display: inline-block;
    transition: all 0.2s ease;
  }

  .btn-secondary:hover {
    background: #616161;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
  }

  h2 {
    color: #333;
    font-size: 1.75rem;
    margin: 0 0 1rem;
  }

  p {
    color: #666;
    font-size: 1rem;
    line-height: 1.6;
    margin: 0.5rem 0;
  }
</style>
