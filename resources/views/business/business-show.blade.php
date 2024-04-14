@extends('admin.layouts.master')
@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Business Detail</h3>
    </div>
    <div class="card-body">
          <p>Porfile Image: <img src="{{$business->profile_image}}" width="100px"/></p>
          <p>Name: {{$business->name}}</p>
          <p>Email: {{$business->email}}</p>
          <p>Opening Time: {{$business->restaurant_opening_time}}</p>
          <p>Closing Time: {{$business->restaurant_closing_time}} </p>
          <p>No of Tables: {{$business->restaurant_max_table}}</p>
    </div>
    <!-- /.card-body -->
</div>

<div class="card">
    <div class="card-header">
        <h3 class="card-title">Restaurant Detail</h3>
    </div>
    <div class="card-body">
        <?php
            $restaurant = $business->restaurant;
           
        ?>
        <p>Restaurant Name: {{$restaurant->restaurant_name}}</p>
        <p>Restaurant Description: {{$restaurant->restaurant_description}}</p>
        <p>Restaurant Address: {{$restaurant->restaurant_address}}</p>
    </div>
    <!-- /.card-body -->
</div>

<div class="card">
    <div class="card-header">
        <h3 class="card-title">Restaurant Menu</h3>
    </div>
    <div class="card-body">
        <?php
            $menus = $restaurant->menus;
        ?>
        @foreach($menus as $menu)
            <p>Item Image: <img src="{{$menu->restaurant_menu_image}}" width="100px"/></p>
            <p>Item Name: {{$menu->restaurant_name}}</p>
            <p>Item Price: {{$menu->restaurant_menu_price}}</p>
            <p>Item Quantity: {{$menu->restaurant_menu_quantity}}</p>
            <p>Item Description: {{$menu->restaurant_menu_description}}</p>
            <hr>
        @endforeach
    </div>
    <!-- /.card-body -->
</div>
@endSection

@section('js')
@endSection