<!DOCTYPE html>
<html>
	<head>
	    <meta charset="utf-8" />
	    <style type="text/css">
	    	.row{
	    		width: 100%;
	    	}
	    	table{width: 100%;text-align: center;border-collapse: collapse;}
	    	table tr td{border: 1px solid #000;}
	    	.ms-1{margin-left: 5px;}
	    	.mb-1{margin-bottom: 10px;}
	    	h4{margin:5px}
	    </style>
	</head>

	<body>
		<div class="row">
			<h3 style="margin-bottom: 10px;text-align: center;">||shreeji||</h3>
			<h4><b>Customer Name:</b> <span class="ms-1">{{$userName}}</span></h4>
			<h4><b>Order Id:</b> <span class="ms-1">{{$orderInfo['id']}}</span></h4>
			<h4><b>Total Weight:</b> <span class="ms-1">{{$totalWeight}}</span></h4>
			<h4><b>Created Date:</b> <span class="ms-1">{{config_date($orderInfo['created_at'])}}</span></h4>
		</div>
		<div class="row" style="margin-top:10px;">
			@if(!empty($orderInfo))
			@foreach($orderInfo['details'] as $item)
			<table class="mb-1">
				<tr>
					<td rowspan="4">
						<img src="{{$item['thumb_image']}}" alt="logo" style="width:50px;height:auto;" />
					</td>
					<td><b>Code:</b><span class="ms-1">{{$item['code']}}</span></td>
				</tr>
				<tr>
					<td><b>Quantity:</b><span class="ms-1">{{$item['quantity']}}</span></td>
				</tr>
				<tr>
					<td><b>Weight:</b><span class="ms-1">{{$item['weight']}}</span></td>
				</tr>
				<tr>
					<td><b>Total:</b><span class="ms-1">{{$item['quantity'] * $item['weight']}}</span></td>
				</tr>
			</table>
			@endforeach
			@endif
		</div>
	</body>
</html>