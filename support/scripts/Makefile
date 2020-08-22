
BASEDIR := ../..

$(BASEDIR)/app/locale/%/messages.mo: $(BASEDIR)/app/locale/%/messages.po
	msgfmt --output-file=$@ $<

TARGETS = $(wildcard $(BASEDIR)/app/locale/*/messages.po)

debug:
	echo "$(TARGETS)"

all: $(TARGETS)
	for i in $(<D):

.PHONY: debug

# po/fr/$(EXEC).po: po/$(EXEC).pot
#     msgmerge --update $@ $<
#
# po/$(EXEC).pot: $(EXEC).c
#     xgettext -k_ -j -lC -c -s -o po/hello.pot hello.c
#
# clean:
#     @rm -f $(EXEC) po/fr/*.mo *~
#
# .PHONY: clean