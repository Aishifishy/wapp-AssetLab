@foreach($getFlashMessages() as $flash)
    <x-alert :type="$flash['type']" :message="$flash['message']" dismissible />
@endforeach
