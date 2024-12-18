<?php
if (isset($_SESSION['show_back']) && $_SESSION['show_back']): 
    unset($_SESSION['show_back']);
?>
<div class="back-button-container">
    <button onclick="history.back()" class="btn-back">
        <i class="fas fa-arrow-left"></i> Back
    </button>
</div>

<style>
.back-button-container {
    margin-bottom: 1rem;
}

.btn-back {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.5rem 1rem;
    background: var(--medium-bg);
    border: none;
    border-radius: 4px;
    color: var(--text-primary);
    cursor: pointer;
    transition: all 0.3s ease;
}

.btn-back:hover {
    background: var(--light-bg);
    color: var(--accent-orange);
}

.btn-back i {
    font-size: 0.9rem;
}
</style>
<?php endif; ?>