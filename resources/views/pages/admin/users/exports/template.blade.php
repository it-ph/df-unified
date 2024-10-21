<table>
    <thead>
    <tr>
        <th style="width: 200px; text-align: center; background-color: #00599D; color: white">first_name</th>
        <th style="width: 200px; text-align: center; background-color: #00599D; color: white">last_name</th>
        <th style="width: 200px; text-align: center; background-color: #00599D; color: white">email_address</th>
        <th style="width: 300px; text-align: center; background-color: #00599D; color: white">roles</th>
        @if($user == 'admin')<th style="width: 200px; text-align: center; background-color: #00599D; color: white">client</th>@endif
    </tr>
    </thead>
    <tbody>
        <tr>
            <td colspan="3" style="color: red; font-weight: bold"> Note: DO NOT Delete the HEADER. (delete all notes)</td>
        </tr>
        <tr>
            <td colspan="3" style="color: red; font-weight: bold"> Note: Roles must be separated by a comma. E.g. Manager,Team Lead,Proofreader,Designer</td>
        </tr>
    </tbody>
</table>
