#
# @Copyright © 2002-2019 Acronis International GmbH. All rights reserved
#

.PHONY: all clean image build test
CURRENT_DIR = $(shell pwd | sed -e "s/^\/mnt//")
IMAGE = whmcs_image
CONTAINER = whmcs_container

CONTAINER_TARGET_PATH = /mnt/target

all: build

image:
	docker build -t $(IMAGE) -f Dockerfile .

build-package:
	docker run --name $(CONTAINER)-$(BUILD_NUMBER) -i --rm \
		-v '$(CURRENT_DIR):$(CONTAINER_TARGET_PATH)' \
		$(IMAGE) \
		bash $(CONTAINER_TARGET_PATH)/build.sh

build: image build-package
