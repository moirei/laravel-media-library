export default {
  body: [
    {
      property: 'file',
      type: 'file',
      required: 'true',
      description: 'The file to be uploaded',
    },
    {
      property: 'location',
      type: 'string',
      required: 'false',
      description: `
        The location in which the file will be saved. A folder will be created if it does\t exist.
        If your folder already exists, it's preferred to provide its ID. If using a path name, make sure the location does not include the package's storage path nor your workspace.
      `,
    },
  ],
  response: {
    name: 'File',
    route: '/data.html#file',
  },
}