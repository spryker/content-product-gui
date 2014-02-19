{%- for environment, environment_details in pillar.environments.items() %}
# Directories
/data/shop/{{ environment }}/shared/Generated:
  file.directory:
    - user: www-data
    - group: www-data
    - dir_mode: 755
    - file_mode: 755
    - makedirs: true
    - recurse:
      - user
      - group
      - mode

/data/shop/{{ environment }}/shared/data/common:
  file.directory:
    - user: www-data
    - group: www-data
    - dir_mode: 755
    - file_mode: 755
    - makedirs: true
    - recurse:
      - user
      - group
      - mode

/data/logs/{{ environment }}:
  file.directory:
    - user: www-data
    - group: www-data
    - dir_mode: 755
    - file_mode: 755
    - makedirs: true
    - recurse:
      - user
      - group
      - mode

/data/storage/{{ environment }}/static:
  file.directory:
    - user: www-data
    - group: www-data
    - dir_mode: 755
    - file_mode: 755
    - makedirs: true
    - recurse:
      - user
      - group
      - mode

/data/shop/{{ environment }}/shared/data/static:
  file.symlink:
    - target: /data/storage/{{ environment }}/static
    - force: true
    - require:
      - file: /data/shop/{{ environment }}/shared/data/common
      - file: /data/storage/{{ environment }}/static

# Application config
/data/shop/{{ environment }}/shared/config_local.php:
  file.managed:
    - source: salt://app/files/config/config_local.php
    - template: jinja
    - user: www-data
    - group: www-data
    - mode: 640
    - require:
      - file: /data/shop/{{ environment }}/shared/data/common
    - context:
      environment: {{ environment }}
      environment_details: {{ environment_details }}

{%- if 'web' in grains.roles %}
# FPM config
/etc/php5/fpm/pool.d/{{ environment }}-zed.conf:
  file.managed:
    - source: salt://app/files/fpm/zed.conf
    - template: jinja
    - user: root
    - group: root
    - mode: 644
    - context:
      environment: {{ environment }}
/etc/php5/fpm/pool.d/{{ environment }}-yves.conf:
  file.managed:
    - source: salt://app/files/fpm/yves.conf
    - template: jinja
    - user: root
    - group: root
    - mode: 644
    - context:
      environment: {{ environment }}


# NginX configs
/etc/nginx/conf.d/{{ environment }}-backend.conf:
  file.managed:
    - source: salt://app/files/nginx/conf.d/backend.conf
    - template: jinja
    - user: root
    - group: root
    - mode: 644
    - context:
      environment: {{ environment }}
{%- endif %}
{%- endfor %}

