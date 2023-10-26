
<table class="table table-hover table-striped ">
    <thead @if(env("APP_table_Color")) class="table-dark" style="background: {{ env("APP_table_Color") }}" @else class="thead-dark" @endif>
        <tr>
            <th scope="col">@lang('main.visitors') - Parameter</th>
            <th scope="col">@lang('main.visit') - Parameter</th>
            <th scope="col">@lang('main.employee') - Parameter</th>
            <th scope="col">@lang('email.function') - Parameter</th>
        </tr>
    </thead>
    <tbody class="table-bordered">
        <tr>
                <td>@lang('email.salutationVisitorLegend')</td>
                <td>@lang('email.startDateLegend')</td>
                <td>@lang('email.nameEmployeeLegend')</td>
                <td>@lang('email.qrCodeLegend')</td>
        </tr>
        <tr>
                <td>@lang('email.forenameVisitorLegend')</td>
                <td>@lang('email.startTimeLegend')</td>
                <td>@lang('email.mobileNumberEmployeeLegend')</td>
                <td>@lang('email.qrCodeVisitorListLegend')</td>
        </tr>
        <tr>
                <td>@lang('email.surnameVisitorLegend')</td>
                <td>@lang('email.endDateLegend')</td>
                <td>@lang('email.landLineNumberEmployeeLegend')</td>
                <td>@lang('email.permissionTypeLegend')</td>
        </tr>
        <tr>
                <td>@lang('email.titleVisitorLegend')</td>
                <td>@lang('email.endTimeLegend')</td>
                <td>@lang('email.departmentEmployeeLegend')</td>
                <td></td>
        </tr>
        <tr>
                <td>@lang('email.dearLegend')</td>
                <td>@lang('email.visitCanteenNumberLegend')</td>
                <td>@lang('email.emailEmployeeLegend')</td>
                <td></td>
        </tr>
        <tr>
            <td>@lang('email.visitCanteenListLegend')</td>
            <td>@lang('email.visitIDLegend')</td>
            <td>@lang('email.approverName')</td>
            <td></td>
        </tr>
        <tr>
            <td>@lang('email.visitorListLegend')</td>
            <td>@lang('email.reasonForVisitLegend')</td>
            <td></td>
            <td></td>
        </tr>
        <tr>
            <td>@lang('email.visitorList')</td>
            <td>@lang('email.reasonForEntryPermissionLegend')</td>
            <td></td>
            <td></td>
        </tr>
        <tr>
            <td></td>
            <td>@lang('email.reasonForWorkPermissionLegend')</td>
            <td></td>
            <td></td>
        </tr>
    </tbody>
</table>
