@extends('backend.layouts.backend')

@section('menu') Pages
@stop

@section('content')
<div class="row" ng-controller="PageController">
    <div class="card">
        <div class="card-header ">
            <h4 class="card-title">
                Page Informations |  <a href="{{ route('backend.pages.detail')}}" class="btn btn-sm bg-dark"> Create New Pages</a>
            </h4>
        </div>
        <div class="card-block">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped m-b-0">
                        <thead>
                            <tr>
                                <th width="130">
                                    Name
                                </th>
                                <th  width="50">
                                    Status
                                </th>
                                <th class="text-center" align="center" width="100" style="text-align: center;">
                                   Action
                                </th>
                            </tr>
                        </thead>
                        <tfoot>
                            <tr>
                                <th width="130">
                                    Name
                                </th>
                                <th  width="50">
                                    Status
                                </th>
                                <th class="text-center" align="center" width="100" style="text-align: center;">
                                   Action
                                </th>
                            </tr>
                        </tfoot>
                        <tbody block-ui="blockui-effect">
                             <tr class="items ng-cloak" ng-repeat="list in data track by $index">
                                <td>
                                     <div uib-tooltip="@{{ list.title }}"> @{{ list.title | truncateStr:35 }} </div>
                                </td>
                                <td>
                                    @{{ list.is_enabled == '1' ? 'Enabled' : "Disabled"}}
                                </td>
                                <td class="text-center" align="center">
                                    <a uib-tooltip="Edit @{{ list.title }}" href="@{{ list.backend_detail_url }}" class="btn btn-sm  btn-info">
                                         <i class="fa fa-pencil"></i>
                                          Edit
                                    </a>
                                    @if($isDemo)
                                       @include('backend.layouts.common.demo-btn')
                                    @else
                                        <button uib-tooltip="Delete @{{ list.title }}" ng-click="delModal(list)" class="btn btn-sm  btn-danger">
                                           <i class="fa fa-remove"></i>
                                         </button>
                                    @endif
                                </td>
                            </tr>
                            <tr ng-if="data.length == 0">
                              <td class="text-center" align="center" mode="simple" colspan="3">No Result Found!</td>
                            </tr>
                            
                        </tbody>
                    </table>
                    <nav class="pull-right">
                      <pagination-directive class="pull-right"></pagination-directive>
                    </nav>
                </div>
            </div>
    </div>
</div>

@stop
@include('backend.page.partials.js')