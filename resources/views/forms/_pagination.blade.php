<?php
/**
 * @var array                                       $frd
 * @var \Illuminate\Pagination\LengthAwarePaginator $elements
 */
?>

<div class='row justify-content-center mt-3'>
    {{ $elements->appends($frd)->links() }}
</div>
