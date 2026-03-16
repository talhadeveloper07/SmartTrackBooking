import React from 'react';
import BookingSummary from './BookingSummary';

export default function StepSelectDuration({
    currentItem,
    durationOptions = [],
    savedItems = [],
    onSelectDuration,
    onBack,
    onContinue,
}) {
    const canContinue = !!currentItem.durationId;

    return (
        <div className="container py-5">
            <div className="row g-4">
                {/* Left Side */}
                <div className="col-lg-8">
                    <div className="card shadow-sm border-0 rounded-4">
                        <div className="card-body p-4">
                            <div className="mb-4">
                                <h2 className="fw-bold mb-2">Select Service Duration</h2>
                                <p className="text-muted mb-0">
                                    Choose the duration for your selected service.
                                </p>
                            </div>

                            <div className="card border-0 bg-light rounded-4 mb-4">
                                <div className="card-body d-flex align-items-center gap-3">
                                    <img
                                        src={currentItem.serviceImage}
                                        alt={currentItem.serviceName}
                                        style={{
                                            width: '90px',
                                            height: '90px',
                                            objectFit: 'cover',
                                            borderRadius: '16px',
                                        }}
                                    />
                                    <div>
                                        <div className="text-muted small">Selected Service</div>
                                        <h5 className="mb-0 fw-bold">{currentItem.serviceName}</h5>
                                    </div>
                                </div>
                            </div>

                            <div className="row g-4">
                                {durationOptions.map((duration) => {
                                    const isSelected = currentItem.durationId === duration.id;

                                    return (
                                        <div className="col-md-6 col-xl-4" key={duration.id}>
                                            <div
                                                className={`card h-100 border duration-card ${
                                                    isSelected ? 'border-primary shadow' : 'border-light'
                                                }`}
                                                style={{
                                                    cursor: 'pointer',
                                                    borderRadius: '20px',
                                                    transition: 'all 0.2s ease',
                                                }}
                                                onClick={() => onSelectDuration(duration)}
                                            >
                                                <div className="card-body text-center p-4">
                                                    <h4 className="fw-bold mb-2">
                                                        {duration.duration_minutes} min
                                                    </h4>

                                                    <div className="text-muted mb-3">
                                                        Service Duration
                                                    </div>

                                                    <div className="fs-5 fw-semibold text-primary mb-3">
                                                        ${Number(duration.price || 0).toFixed(2)}
                                                    </div>

                                                    {isSelected ? (
                                                        <span className="badge bg-primary px-3 py-2 rounded-pill">
                                                            Selected
                                                        </span>
                                                    ) : (
                                                        <span className="badge bg-light text-dark px-3 py-2 rounded-pill">
                                                            Select
                                                        </span>
                                                    )}
                                                </div>
                                            </div>
                                        </div>
                                    );
                                })}
                            </div>

                            {durationOptions.length === 0 && (
                                <div className="alert alert-warning rounded-4 mt-3">
                                    No durations found for this service.
                                </div>
                            )}

                            <div className="d-flex justify-content-between mt-4">
                                <button
                                    type="button"
                                    className="btn btn-outline-secondary px-4 py-2 rounded-pill"
                                    onClick={onBack}
                                >
                                    Back
                                </button>

                                <button
                                    type="button"
                                    className="btn btn-primary px-4 py-2 rounded-pill"
                                    disabled={!canContinue}
                                    onClick={onContinue}
                                >
                                    Continue
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                {/* Right Side */}
                <div className="col-lg-4">
                    <BookingSummary
                        savedItems={savedItems}
                        currentItem={currentItem}
                    />
                </div>
            </div>
        </div>
    );
}