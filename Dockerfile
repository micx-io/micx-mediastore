FROM ghcr.io/nfra-project/kickstart-flavor-php:unstable
ENV DEV_CONTAINER_NAME="micx-mediastore"


ADD / /opt
RUN ["bash", "-c",  "chown -R user /opt"]
RUN ["/kickstart/run/entrypoint.sh", "build"]

ENTRYPOINT ["/kickstart/run/entrypoint.sh", "standalone"]
