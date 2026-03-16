import React from 'react';
import BookingSummary from './BookingSummary';

export default function AuthStep({
    savedItems = [],
    authMode,
    setAuthMode,
    authData,
    setAuthData,
    onBack,
    onAddMoreService,
    onContinue,
    onRemoveItem,
}) {
    const canContinue =
        authMode === 'login'
            ? authData.email && authData.password
            : authData.first_name &&
              authData.last_name &&
              authData.email &&
              authData.phone &&
              authData.password;

    const updateField = (key, value) => {
        setAuthData((prev) => ({
            ...prev,
            [key]: value,
        }));
    };

    return (
        <div className="container py-5">
            <div className="row g-4">
                <div className="col-lg-8">
                    <div className="card shadow-sm border-0 rounded-4">
                        <div className="card-body p-4">
                            <div className="mb-4">
                                <h2 className="fw-bold mb-2">Step 5: Login or Create Account</h2>
                                <p className="text-muted mb-0">
                                    Continue with your account to complete the booking.
                                </p>
                            </div>

                            <div className="d-flex gap-2 mb-4">
                                <button
                                    type="button"
                                    className={`btn rounded-pill px-4 ${
                                        authMode === 'login'
                                            ? 'btn-primary'
                                            : 'btn-outline-secondary'
                                    }`}
                                    onClick={() => setAuthMode('login')}
                                >
                                    Login
                                </button>

                                <button
                                    type="button"
                                    className={`btn rounded-pill px-4 ${
                                        authMode === 'register'
                                            ? 'btn-primary'
                                            : 'btn-outline-secondary'
                                    }`}
                                    onClick={() => setAuthMode('register')}
                                >
                                    Create Account
                                </button>
                            </div>

                            {authMode === 'login' ? (
                                <div className="row g-3">
                                    <div className="col-md-12">
                                        <label className="form-label">Email</label>
                                        <input
                                            type="email"
                                            className="form-control rounded-4"
                                            value={authData.email}
                                            onChange={(e) => updateField('email', e.target.value)}
                                        />
                                    </div>

                                    <div className="col-md-12">
                                        <label className="form-label">Password</label>
                                        <input
                                            type="password"
                                            className="form-control rounded-4"
                                            value={authData.password}
                                            onChange={(e) => updateField('password', e.target.value)}
                                        />
                                    </div>
                                </div>
                            ) : (
                                <div className="row g-3">
                                    <div className="col-md-6">
                                        <label className="form-label">First Name</label>
                                        <input
                                            type="text"
                                            className="form-control rounded-4"
                                            value={authData.first_name}
                                            onChange={(e) => updateField('first_name', e.target.value)}
                                        />
                                    </div>

                                    <div className="col-md-6">
                                        <label className="form-label">Last Name</label>
                                        <input
                                            type="text"
                                            className="form-control rounded-4"
                                            value={authData.last_name}
                                            onChange={(e) => updateField('last_name', e.target.value)}
                                        />
                                    </div>

                                    <div className="col-md-6">
                                        <label className="form-label">Email</label>
                                        <input
                                            type="email"
                                            className="form-control rounded-4"
                                            value={authData.email}
                                            onChange={(e) => updateField('email', e.target.value)}
                                        />
                                    </div>

                                    <div className="col-md-6">
                                        <label className="form-label">Phone</label>
                                        <input
                                            type="text"
                                            className="form-control rounded-4"
                                            value={authData.phone}
                                            onChange={(e) => updateField('phone', e.target.value)}
                                        />
                                    </div>

                                    <div className="col-md-12">
                                        <label className="form-label">Password</label>
                                        <input
                                            type="password"
                                            className="form-control rounded-4"
                                            value={authData.password}
                                            onChange={(e) => updateField('password', e.target.value)}
                                        />
                                    </div>
                                </div>
                            )}

                            <div className="d-flex justify-content-between align-items-center mt-4 flex-wrap gap-2">
                                <div className="d-flex gap-2">
                                    <button
                                        type="button"
                                        className="btn btn-outline-secondary px-4 py-2 rounded-pill"
                                        onClick={onBack}
                                    >
                                        Back
                                    </button>

                                    <button
                                        type="button"
                                        className="btn btn-outline-primary px-4 py-2 rounded-pill"
                                        onClick={onAddMoreService}
                                    >
                                        + Add More Service
                                    </button>
                                </div>

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

                <div className="col-lg-4">
                    <BookingSummary
                        savedItems={savedItems}
                        onRemoveItem={onRemoveItem}
                    />
                </div>
            </div>
        </div>
    );
}