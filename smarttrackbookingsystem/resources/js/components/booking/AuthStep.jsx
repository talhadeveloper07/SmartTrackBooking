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
    loading = false,
    error = '',
}) {
    const updateField = (key, value) => {
        setAuthData((prev) => ({
            ...prev,
            [key]: value,
        }));
    };

    const canContinue =
        authMode === 'login'
            ? authData.email && authData.password
            : authData.first_name &&
              authData.last_name &&
              authData.email &&
              authData.phone &&
              authData.password;

    return (
        <div className="container py-5">
            <div className="row g-4">
                <div className="col-lg-8">
                    <div className="card shadow-sm border-0 rounded-4">
                        <div className="card-body p-4">
                            <div className="mb-4">
                                <h2 className="fw-bold mb-2">Login or Create Account</h2>
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

                            {error ? (
                                <div className="alert alert-danger rounded-4">
                                    {error}
                                </div>
                            ) : null}

                            {authMode === 'login' ? (
                                <div className="row g-3">
                                    <div className="col-md-6">
                                        <label className="form-label fw-semibold">Email</label>
                                        <input
                                            type="email"
                                            className="form-control rounded-4"
                                            placeholder="Enter your email"
                                            value={authData.email}
                                            onChange={(e) => updateField('email', e.target.value)}
                                        />
                                    </div>

                                    <div className="col-md-6">
                                        <label className="form-label fw-semibold">Password</label>
                                        <input
                                            type="password"
                                            className="form-control rounded-4"
                                            placeholder="Enter your password"
                                            value={authData.password}
                                            onChange={(e) => updateField('password', e.target.value)}
                                        />
                                    </div>
                                </div>
                            ) : (
                                <div className="row g-3">
                                    <div className="col-md-6">
                                        <label className="form-label fw-semibold">First Name</label>
                                        <input
                                            type="text"
                                            className="form-control rounded-4"
                                            placeholder="First name"
                                            value={authData.first_name}
                                            onChange={(e) => updateField('first_name', e.target.value)}
                                        />
                                    </div>

                                    <div className="col-md-6">
                                        <label className="form-label fw-semibold">Last Name</label>
                                        <input
                                            type="text"
                                            className="form-control rounded-4"
                                            placeholder="Last name"
                                            value={authData.last_name}
                                            onChange={(e) => updateField('last_name', e.target.value)}
                                        />
                                    </div>

                                    <div className="col-md-6">
                                        <label className="form-label fw-semibold">Email</label>
                                        <input
                                            type="email"
                                            className="form-control rounded-4"
                                            placeholder="Email address"
                                            value={authData.email}
                                            onChange={(e) => updateField('email', e.target.value)}
                                        />
                                    </div>

                                    <div className="col-md-6">
                                        <label className="form-label fw-semibold">Phone</label>
                                        <input
                                            type="text"
                                            className="form-control rounded-4"
                                            placeholder="Phone number"
                                            value={authData.phone}
                                            onChange={(e) => updateField('phone', e.target.value)}
                                        />
                                    </div>

                                    <div className="col-md-12">
                                        <label className="form-label fw-semibold">Password</label>
                                        <input
                                            type="password"
                                            className="form-control rounded-4"
                                            placeholder="Create password"
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
                                        className="btn btn-outline-secondary rounded-pill px-4"
                                        onClick={onBack}
                                    >
                                        Back
                                    </button>

                                    <button
                                        type="button"
                                        className="btn btn-outline-primary rounded-pill px-4"
                                        onClick={onAddMoreService}
                                    >
                                        + Add More Service
                                    </button>
                                </div>

                                <button
                                    type="button"
                                    className="btn btn-primary rounded-pill px-4"
                                    disabled={!canContinue || loading}
                                    onClick={onContinue}
                                >
                                    {loading ? 'Please wait...' : authMode === 'login' ? 'Login' : 'Create Account'}
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