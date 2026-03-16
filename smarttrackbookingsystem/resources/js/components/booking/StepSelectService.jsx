import React from 'react';
import BookingSummary from './BookingSummary';

export default function StepSelectService({
    services = [],
    currentItem,
    savedItems = [],
    onSelectService,
    onContinue,
}) {
    const canContinue = !!currentItem.serviceId;

    return (
        <div className="container py-5">
            <div className="row g-4">
                {/* Left Side */}
                <div className="col-lg-8">
                    <div className="card shadow-sm border-0 rounded-4">
                        <div className="card-body p-4">
                            <div className="mb-4">
                                <h2 className="fw-bold mb-2">Select Service</h2>
                                <p className="text-muted mb-0">
                                    Choose one service to start this booking journey.
                                </p>
                            </div>

                            <div className="row g-4">
                                {services.map((service) => {
                                    const isSelected = currentItem.serviceId === service.id;

                                    return (
                                        <div className="col-md-6 col-xl-4" key={service.id}>
                                            <div
                                                className={`card h-100 border service-card ${
                                                    isSelected ? 'border-primary shadow' : 'border-light'
                                                }`}
                                                style={{
                                                    cursor: 'pointer',
                                                    borderRadius: '20px',
                                                    transition: 'all 0.2s ease',
                                                    overflow: 'hidden',
                                                }}
                                                onClick={() => onSelectService(service)}
                                            >
                                               

                                                <div className="card-body text-center">
                                                    <h5 className="fw-bold mb-2">{service.name}</h5>

                                                    {service.short_description && (
                                                        <p className="text-muted small mb-3">
                                                            {service.short_description}
                                                        </p>
                                                    )}

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

                            <div className="d-flex justify-content-end mt-4">
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