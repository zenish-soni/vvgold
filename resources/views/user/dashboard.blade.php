@extends('layouts/user_master')
@section('title',$title)
@section('content')
<div class="row">
<!-- Statistics Cards -->
	<div class="col-4 col-md-4 col-lg-4 mb-4">
		<div class="card h-100">
			<div class="card-body text-center">
				<div class="avatar mx-auto mb-2">
					<span class="avatar-initial rounded-circle bg-label-success"><i class="bx bx-cart fs-4"></i></span>
				</div>
				<span class="d-block text-nowrap">Orders</span>
				<h2 class="mb-0">{{$totalOrder}}</h2>
			</div>
		</div>
	</div>

	<div class="col-4 col-md-4 col-lg-4 mb-4">
		<div class="card h-100">
			<div class="card-body text-center">
				<div class="avatar mx-auto mb-2">
					<span class="avatar-initial rounded-circle bg-label-danger"><i class='bx bxl-product-hunt'></i></span>
				</div>
				<span class="d-block text-nowrap">Products</span>
				<h2 class="mb-0">{{$totalProduct}}</h2>
			</div>
		</div>
	</div>

	<div class="col-4 col-md-4 col-lg-4 mb-4">
		<div class="card h-100">
			<div class="card-body text-center">
				<div class="avatar mx-auto mb-2">
					<span class="avatar-initial rounded-circle bg-label-danger"><i class='bx bx-group'></i></span>
				</div>
				<span class="d-block text-nowrap">Users</span>
				<h2 class="mb-0">{{$totalUser}}</h2>
			</div>
		</div>
	</div>
</div>
@endsection