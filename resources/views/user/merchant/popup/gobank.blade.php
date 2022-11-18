<script>
    var channel = new BroadcastChannel('bank');
    channel.postMessage("{{route('go.back')}}");
    console.log(channel);
    window.close();
</script>