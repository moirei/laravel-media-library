# Routes

All names are prepended with packages route name. E.g. `media.file`.

The package route name can be changes in the config.



| Name                 | Description                                                  |
| -------------------- | ------------------------------------------------------------ |
| `file`               | File access route.                                           |
| `file.signed`        | File access route signed. Generated urls are temporal.       |
| `download`           | File download route.                                         |
| `download.signed`    | File download route signed.                                  |
| `stream`             | File stream route.                                           |
| `stream.signed`      | File stream route signed.                                    |
| `file.protected`     | File access route protected. Intended for private files and should be exclusive to admins. |
| `download.protected` | File download route protected. Intended for private files and should be exclusive to admins. |
| `stream.protected`   | File stream route protected. Intended for private files and should be exclusive to admins. |
| `upload`             | Upload route, protected by default. Should be exclusive to admins. |
| `update`             | File update request route, protected by default. Should be exclusive to admins. |
| `move`               | File move request route, protected by default. Should be exclusive to admins. |
| `delete`             | File delete request route, protected by default. Should be exclusive to admins. |
| `folder.create`      | Folder creation route, protected by default. Should be exclusive to admins. |
| `folder.update`      | Folder update request route, protected by default. Should be exclusive to admins. |
| `folder.move`        | Folder move request route, protected by default. Should be exclusive to admins. |
| `folder.delete`      | Folder delete request route, protected by default. Should be exclusive to admins. |
| `browse`             | Browse files and folders routes, protected by default. Should be exclusive to admins. |
| `shareable-link`     | Get shareable link route, protected by default. Should be exclusive to admins. |
| `downloadable-link` | Get downloadable link route, protected by default. Should be exclusive to admins. |
| `attachment.store` | Attachment upload route, protected by default. Should be exclusive to admins. |
| `attachment.destroy` | Destroy uploaded attachment route, protected by default. Should be exclusive to admins. |
| `share` | Shared content route. |
| `share.auth` | Shared content authentication route. |
| `share.auth.post` | Shared content authentication submit route. |
| `share.download` | Shared content download item route. |
| `share.upload` | Shared content upload route. |

