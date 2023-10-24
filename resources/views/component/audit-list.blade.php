<table class="auditList" style="min-width:100%;table-layout:fixed;">
  <thead>
    <tr style="background:rgba(0, 0, 0, 0.11);">
      <th>Időpont</th>
      <th>Felhasználó</th>
      <th>Mennyiség</th>
      <th>Bruttó ár</th>
      <th>Nettó ár</th>
      <th>Forgalmazó</th>
      <th>Besorolás</th>
      <th>Megjegyzés</th>
      <th style="width:90px;"></th>
    </tr>
  </thead>
  <tbody>
    @foreach ($data->audits as $auditItem)
      <tr>
        <td>{{ $auditItem->created_at ?? '' }}</td>
        <td>{{ $auditItem->user->name ?? '' }}</td>
        <td style="text-align: right">{{ $auditItem->quantity ?? '' }} db</td>
        <td style="text-align: right">{{ $auditItem->price_gross ?? '' }} Ft</td>
        <td style="text-align: right">{{ $auditItem->price_net ?? '' }} Ft</td>
        <td>{{ $auditItem->distributor->name ?? '' }}</td>
        <td>{{ implode(', ', $auditItem->tags->pluck('name')->toArray()) ?? '' }}</td>
        <td>{{ $auditItem->properties->note ?? '' }}</td>
        <td style="padding:0;">
          <a class="button s" href="{{ route('audit.edit', ['id' => $auditItem->id]) }}" title="Szerkesztés"><i class="fa fa-pencil"></i></a>
          <a class="button s" href="{{ route('audit.delete', ['id' => $auditItem->id]) }}" title="Törlés"><i class="fa fa-trash"></i></a>
        </td>
      </tr>
    @endforeach
  </tbody>
</table>
