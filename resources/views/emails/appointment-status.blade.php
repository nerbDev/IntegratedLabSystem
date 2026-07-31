<h2>Hello {{ $appointment->first_name }},</h2>
<p>Your appointment scheduled on {{ $appointment->appointment_date }} has been <strong>{{ $status }}</strong>.</p>
@if($appointment->status === 'rescheduled')
<p>New date: {{ $appointment->new_date }}</p>
@endif
<p>Please log in to your account for more details.</p>