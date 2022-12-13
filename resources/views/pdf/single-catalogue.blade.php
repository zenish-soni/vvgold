<!DOCTYPE html>
<html>
	<head>
	    <meta charset="utf-8" />
	    <meta name="viewport" content="width=device-width, initial-scale=1">
	    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@500&display=swap" rel="stylesheet" />
	    <style type="text/css">
	    	@page{margin:0px;padding: 0px;}
	    	body{
	    		border-top-width: 30px;
	    		border-bottom-width: 30px;
	    		border-right-width: 20px;
	    		border-left-width: 20px;
	    		border-color: #c69a1d;
	    		border-style: solid;
				background-color: rgba(0,0,0,0.9);
				padding: 10px;
	    		color:#fff;
	    		font-size: 14px;
	    		font-weight: bold;
	    		font-family: 'Roboto', sans-serif;
	    	}
	    	.row{
	    		width: 100%;
	    	}
	    	table{text-align: center;border-collapse: collapse;float: left;padding:0 2%; width: 100%}
	    	table tr th,table tr td{border: 2px solid #c69a1d;padding:5px; }
	    	table tr th{font-size: 18px;}
	    	.ms-1{margin-left: 5px;}
	    	.mb-1{margin-bottom: 10px;}
	    	h4{margin:5px}

	    	#watermark {
	            position: fixed;
	            bottom:   20%;
	            left:     25%;

	            /** Change image dimensions**/
	            width:    12cm;
	            height:   12cm;

	            /** Your watermark should be behind every content**/
	            z-index:  -1000;
	             opacity: 0.3; 
	        }
	    </style>
	</head>

	<body>
		 <div id="watermark" class="">
        <img src="{{ public_path('img/logo.png') }}" height="auto" width="400px" />
    </div>
		<div class="row">
			@if(!empty($records))
			<table class="mb-1">
				<thead>
					<tr>
						<th>NO</th>
						<th>BEADS</th>
						<th>SIZE</th>
						<th>APPROX. WT</th>
					</tr>
				</thead>
				@foreach($records as $item)
				<tbody>
					<tr>
						<td>{{$item['code']}}</td>
						<td><img src="{{$item['thumb_image']}}" alt="logo" style="width:100px;height:auto;" /></td>
						<td>{{$item['size_name']}}</td>
						<td>{{$item['weight']}}</td>
					</tr>
				</tbody>
				@endforeach
			</table>
			@endif
		</div>
	</body>
</html>