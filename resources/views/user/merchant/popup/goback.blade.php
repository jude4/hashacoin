<script>
    var channel = new BroadcastChannel('payment');
    channel.postMessage("{{route('generate.receipt', ['id' => $ref])}}");
    console.log(channel);
    window.close();
</script>