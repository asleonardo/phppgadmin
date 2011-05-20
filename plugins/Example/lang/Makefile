# This Makefile is a copy of lang/Makefile, it recodes source lang files into XML Unicode.
# You should add your encodings to this file.  You need to have GNU Recode
# installed.
# 
# It is important to:
# - fix the delimiters of php code: <?php and ?>
# - convert CRLF -> LF
# - remove all text before first <?php
# - remove all text after last ?>
# - convert "&apos;" -> "'"
# using 'convert.awk'

DESTDIR=./recoded
TARGETS=english \
	portuguese-br \

all:	prepare ${TARGETS}
	@for p in ${TARGETS} ; do chmod 644 ${DESTDIR}/$$p.php ; done

prepare:
	@which recode >/dev/null 2>&1 || ( echo "You must have GNU 'recode' installed to use this Makefile,\
						but I could not find it in your path!" && exit 1 )
	@which sed >/dev/null 2>&1 || ( echo "You must have sed installed to use this Makefile,\
						but I could not find it in your path!" && exit 1 )
	@which awk >/dev/null 2>&1 || ( echo "You must have awk installed to use this Makefile,\
						but I could not find it in your path!" && exit 1 )

clean:
	@echo "Nuking recoded lang files..."
	@for p in ${TARGETS} ; do rm -fv ${DESTDIR}/$$p.php ; done

english:	english.php
	@echo "Recoding english..."
	@cat english.php | recode latin1..xml | ./convert.awk \
		> ${DESTDIR}/english.php
	@chmod 644 english.php ${DESTDIR}/english.php

portuguese-br:	portuguese-br.php
	@echo "Recoding portuguese-br..."
	@cat portuguese-br.php | recode iso-8859-1..xml | ./convert.awk \
		> ${DESTDIR}/portuguese-br.php
	@chmod 644 portuguese-br.php ${DESTDIR}/portuguese-br.php
