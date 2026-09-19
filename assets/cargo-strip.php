<!-- ========================================== -->
<!-- CARGO COMMAND STRIP & WIDE STATS TICKER     -->
<!-- ========================================== -->
<section class="velox-cargo-section">
    <div class="cargo-container">
        
        <!-- Wide Horizontal Stat Ticker (Spans wider than the card below) -->
        <div class="cargo-stats-wide">
            <div class="stat-item">
                <span class="stat-number">15+</span>
                <span class="stat-label">Active Terminals</span>
            </div>
            <div class="stat-divider"></div>
            <div class="stat-item">
                <span class="stat-number">99.8%</span>
                <span class="stat-label">On-Time Dispatch</span>
            </div>
            <div class="stat-divider"></div>
            <div class="stat-item">
                <span class="stat-number">24/7</span>
                <span class="stat-label">Real-Time Telemetry</span>
            </div>
        </div>

        <!-- Focused, Slightly Narrower Tracking Command Card -->
        <div class="cargo-command-card">
            <div class="cargo-card-header">
                <span class="cargo-badge">LOOKING FOR A PARCEL?</span>
                <h3>Enter tracking number.</h3>
            </div>

            <!-- Tracking Input Form -->
            <form action="tracking-result.php" method="GET" class="cargo-input-group">
                <div class="input-wrapper">
                    <svg class="tracking-icon" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="11" cy="11" r="8"></circle>
                        <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                    </svg>
                    <input type="text" name="waybill" placeholder="Enter Waybill or Tracking ID (e.g., VLX-9482)" required />
                </div>
                
                <button type="submit" class="btn-track-action">
                    <span>Track Status</span>
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"></polyline></svg>
                </button>
            </form>

            <!-- Subtle Secondary Link Below -->
            <div class="cargo-sub-footer">
                <p>Need to ship a package? <a href="send-cargo.php">Send Cargo ➔</a></p>
            </div>
        </div>

    </div>
</section>