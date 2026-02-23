{{-- resources/views/business/admin/settings/tabs/notifications.blade.php --}}
<div class="tab-pane fade" id="notifications" role="tabpanel" aria-labelledby="notifications-tab">
    <form action="{{ route('business.settings.notifications', $business->slug) }}" method="POST">
        @csrf
        @method('PUT')
        
        <h5 class="mb-3">Notification Preferences</h5>
        
        <div class="mb-4">
            <div class="form-check form-switch mb-2">
                <input class="form-check-input" type="checkbox" id="email_notifications" 
                       name="email_notifications" value="1" {{ $settings['email_notifications'] ? 'checked' : '' }}>
                <label class="form-check-label" for="email_notifications">Email Notifications</label>
            </div>
            
            <div class="form-check form-switch mb-2">
                <input class="form-check-input" type="checkbox" id="sms_notifications" 
                       name="sms_notifications" value="1" {{ $settings['sms_notifications'] ? 'checked' : '' }}>
                <label class="form-check-label" for="sms_notifications">SMS Notifications</label>
            </div>
            
            <div class="form-check form-switch mb-2">
                <input class="form-check-input" type="checkbox" id="push_notifications" 
                       name="push_notifications" value="1" {{ $settings['push_notifications'] ? 'checked' : '' }}>
                <label class="form-check-label" for="push_notifications">Push Notifications</label>
            </div>
        </div>
        
        <h5 class="mb-3">Notification Events</h5>
        
        <div class="mb-4">
            <div class="form-check">
                <input class="form-check-input" type="checkbox" id="event_new_order" 
                       name="notification_events[]" value="new_order"
                       {{ in_array('new_order', $settings['notification_events']) ? 'checked' : '' }}>
                <label class="form-check-label" for="event_new_order">New Order Received</label>
            </div>
            
            <div class="form-check">
                <input class="form-check-input" type="checkbox" id="event_new_customer" 
                       name="notification_events[]" value="new_customer"
                       {{ in_array('new_customer', $settings['notification_events']) ? 'checked' : '' }}>
                <label class="form-check-label" for="event_new_customer">New Customer Registered</label>
            </div>
            
            <div class="form-check">
                <input class="form-check-input" type="checkbox" id="event_low_stock" 
                       name="notification_events[]" value="low_stock"
                       {{ in_array('low_stock', $settings['notification_events']) ? 'checked' : '' }}>
                <label class="form-check-label" for="event_low_stock">Low Stock Alert</label>
            </div>
            
            <div class="form-check">
                <input class="form-check-input" type="checkbox" id="event_order_status" 
                       name="notification_events[]" value="order_status"
                       {{ in_array('order_status', $settings['notification_events']) ? 'checked' : '' }}>
                <label class="form-check-label" for="event_order_status">Order Status Changed</label>
            </div>
            
            <div class="form-check">
                <input class="form-check-input" type="checkbox" id="event_payment_received" 
                       name="notification_events[]" value="payment_received"
                       {{ in_array('payment_received', $settings['notification_events']) ? 'checked' : '' }}>
                <label class="form-check-label" for="event_payment_received">Payment Received</label>
            </div>
        </div>
        
        <button type="submit" class="btn btn-primary">Save Notification Settings</button>
    </form>
</div>