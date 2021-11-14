<style>

    .option {
        width: 200px;
    }

    .user {
        position: relative;
        cursor: pointer;
    }

    .user:hover .hover-option {
        display: block;
    }

    .hover-option {
        display: none;
        position: absolute;
        top: 40px;
        right: 0;
        padding: 0;
        width: 200px;
        list-style: none;
        font-size: 12px;
        border-radius: 5px;
        z-index: 50;
        background: #fff;
    }

    .hover-option a {
        text-decoration: none;
        color: #000;
    }

    .hover-option li {
        padding: 10px;
        border: 1px solid #b2bec3;
        font-weight: bold;
    }

</style>

<header class="pt-2 pl-5 pb-2 pr-5" style="width: 100%; background: #fff; border-bottom: 1px solid #b2bec3; z-index: 10;">
    <div class="row">
        <div class="col-6">
            <img style="width: 70px;" src="{{ asset('image/pagadian_round.png') }}">
            <span style="font-size: 20px; font-weight: 600" class="ml-2">Online Payment Portal</span>
        </div>
        <div class="col-6 d-flex align-items-center justify-content-end">
            <div class="user d-flex align-items-center p-2" style="height: 50px">
                <i class="fas fa-user-circle" style="font-size: 25px"></i>
                <span class="mr-2 ml-2" style="font-size: 13px;">{{ Auth::user()->name }}</span>
                <span><i class="fas fa-caret-down mt-1"></i></span>

                <ul class="hover-option">
                    <a href="{{ route('logout') }}">
                        <li>
                            <i class="fas fa-power-off mr-2"></i>Logout
                        </li>
                    </a>
                </ul>
            </div>
        </div>
    </div>
</header>


{{--<div class="option text-right border d-flex align-items-center">--}}
{{--    <i class="fas fa-user-circle mt-1" style="font-size: 25px"></i>--}}
{{--    <a href="#" class="user" style="font-size: 17px; text-decoration: none; color: #000; cursor: pointer">--}}
{{--        <span class="mr-2 ml-2" style="font-size: 13px;">{{ Auth::user()->name }}</span>--}}
{{--        <span><i class="fas fa-caret-down"></i></span>--}}
{{--        <ul class="hover-option">--}}
{{--            <li><i class="fas fa-sign-out-alt mr-2"></i> Logout</li>--}}
{{--        </ul>--}}
{{--    </a>--}}
{{--</div>--}}
