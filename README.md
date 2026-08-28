# LessonMark

LessonMark is a Moodle course resource for authoring, previewing, and publishing teaching material whose source of truth remains Markdown.

The first release is planned as the Moodle activity module `mod_lessonmark`.

## Repository status

This repository currently contains only the initial project structure and planning documents. The Moodle plugin and local Docker test environment have not been implemented yet.

## Repository layout

```text
LessonMark/
├── docs/                  Product and technical decisions
├── plugin/
│   └── lessonmark/        Future mod_lessonmark source
├── .vscode/               Shared VS Code settings
└── LessonMark.code-workspace
```

The local Moodle/Docker environment will be added separately in a later step. It is expected to live under a dedicated development directory rather than being mixed into the plugin source.

## Development assumptions

- Host OS: Windows
- Development shell: WSL
- Project path in Windows: `D:\workspace\LessonMark`
- Project path in WSL: `/mnt/d/workspace/LessonMark`
- Editor: VS Code with WSL support
- Containers: Docker Engine running inside WSL
- Docker Desktop: not used
- Initial Moodle target: Moodle 5.2
- Initial PHP targets: PHP 8.3 and 8.4

Open the repository from WSL:

```bash
cd /mnt/d/workspace/LessonMark
code LessonMark.code-workspace
```

## Documents

- [Product requirements](docs/PRODUCT_REQUIREMENTS.md)
- [Technical decisions and milestones](docs/TECHNICAL_DECISIONS.md)

## Planned next step

Add a WSL-native Moodle Docker test environment, then mount or link `plugin/lessonmark` into Moodle as `mod/lessonmark`.

The development environment must keep generated Moodle data, database data, secrets, and local configuration outside Git.

## GitHub

The repository is prepared for Git. A GitHub remote is intentionally not created until the owner, visibility, and repository name are confirmed.

