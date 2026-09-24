<?php
/**
 * Ecosystem Component (assets/ecosystem.php)
 * Left column: Map (70%)
 * Right column: Dynamic height matching marquee with strict 30% width confinement
 */

$operators = [
    "AeroLine", "BluePort", "Cargex", "DeltaHub", 
    "ExpressGo", "FreightPro", "GlobalTrans", "HaulCorp",
    "InterFreight", "JetLogistics", "KineticX", "LineHaul"
];
?>

<section class="ecosystem-section">
    <!-- Section Text Header -->
    <div class="ecosystem-header">
        <h2>Terminal Network</h2>
        <p class="ecosystem-subtitle">Explore active terminal hubs and our trusted logistics partners across the network.</p>
    </div>
 
    <div class="ecosystem-grid">
        <!-- Left Side: Map Container (70%) -->
        <div class="ecosystem-map-container" id="mapContainer">
            <div class="map-placeholder-content">
                <svg viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <polygon points="1 6 1 22 8 18 16 22 23 18 23 2 16 6 8 2 1 6"></polygon>
                    <line x1="8" y1="2" x2="8" y2="18"></line>
                    <line x1="16" y1="6" x2="16" y2="22"></line>
                </svg>
                <p>Interactive Map showing Terminal Pins</p>
            </div>
        </div>
 
        <!-- Right Side: Partner Operators Marquee (30%) -->
        <div class="ecosystem-operators-container" id="operatorsContainer">
            <div class="operators-header-group">
                <h3>Partner Operators</h3>
                <p class="operators-subtext">Want to operate with us? 
                    <a href="auth.php#onboarding" class="register-link">Register <span>&rarr;</span></a>
                </p>
            </div>
 
            <!-- Dynamic Marquee Wrapper -->
            <div class="marquee-wrapper" id="marqueeWrapper">
                <!-- Rows will be injected dynamically via JS -->
            </div>
        </div>
    </div>
</section>

<!-- Dynamic Height & Row Generator Script -->
<script>
document.addEventListener("DOMContentLoaded", function() {
    const mapContainer = document.getElementById('mapContainer');
    const marqueeWrapper = document.getElementById('marqueeWrapper');
    const operators = <?php echo json_encode($operators); ?>;

    function buildMarquee() {
        marqueeWrapper.innerHTML = '';
        
        const mapHeight = mapContainer.offsetHeight;
        const rowHeight = 70; // estimated card height + gap
        const headerOffset = 85; 
        const availableHeight = mapHeight - headerOffset;
        let calculatedRows = Math.floor(availableHeight / rowHeight);
        
        if (calculatedRows < 3) calculatedRows = 4;

        for (let i = 0; i < calculatedRows; i++) {
            const rowDiv = document.createElement('div');
            rowDiv.className = 'marquee-row ' + (i % 2 === 0 ? 'scroll-left' : 'scroll-right');
            
            let rowItems = [...operators, ...operators, ...operators];
            if (i % 2 !== 0) {
                rowItems.reverse();
            }

            rowItems.forEach(op => {
                const card = document.createElement('div');
                card.className = 'operator-card';
                card.textContent = op;
                rowDiv.appendChild(card);
            });

            marqueeWrapper.appendChild(rowDiv);
        }
    }

    buildMarquee();
    window.addEventListener('resize', buildMarquee);
});
</script>