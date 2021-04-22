## Notes
* Private files/folders store in local public disks may still be publically accessible



## Attachments

All attachments a pending until persisted. If your model is setup with `richTextFields` defined, created attachments will be automatically detected and persisted.

### Attachables

Attachments are primarily intended for embedding images in rich-text applications.

It's assumed that an attachment is an image file. Therefore automatically persisting attachments only scans for `img` tags in the rich text fields. Feel free to add more *regex* expressions in `attachments.richtext_match` of your config.



## Namespacing

A `Namespace` value provided in the request header will automatically be used to make the request context. This means a request to access/upload files to folder `products/chargers` will resolve `<namespace>/products/chargers` as the working directory.

This is intended for multi-vendor or strict storage space applications. You can add custom middleware to the media library route to guard your users according to their workspace.

