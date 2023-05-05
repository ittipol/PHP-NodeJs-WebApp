<div class="item-list-panel">
@foreach($data as $_value)
  <?php $value = $_value->buildDataList(); ?>

  @include('shared.item-card-w-menu')
@endforeach
</div>

<script type="text/javascript">

	if(window.innerWidth > 480) {
		$('.item-list-panel').masonry({
		  itemSelector: '.c-grid__col',
		  percentPosition: true
		});
	}
	
	$('.lazy').lazy({
	  effect: "fadeIn",
	  effectTime: 220,
	  threshold: 0
	});
</script>