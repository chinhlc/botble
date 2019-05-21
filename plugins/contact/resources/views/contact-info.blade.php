@if ($contact)
    <p>{{ trans('plugins.contact::contact.tables.fullname') }}: {{ $contact->name }}</p>
    <p>{{ trans('plugins.contact::contact.tables.email') }}: <a href="mailto:{{ $contact->email }}">{{ $contact->email }}</a></p>
    <p>{{ trans('plugins.contact::contact.tables.phone') }}: <a href="tel:{{ $contact->phone }}">{{ $contact->phone }}</a></p>
    <p>{{ trans('plugins.contact::contact.tables.address') }}: {{ $contact->address }}</p>
    <p>{{ trans('plugins.contact::contact.tables.subject') }}: {{ $contact->subject }}</p>
    <p>{{ trans('plugins.contact::contact.tables.content') }}: {{ $contact->content }}</p>
@endif
