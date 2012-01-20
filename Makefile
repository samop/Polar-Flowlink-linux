all: polar

CC       ?= gcc
CFLAGS   ?= -Wall -g

CXX      ?= g++
CXXFLAGS ?= -Wall -g

COBJS     = src/hid-libusb.o src/polar.o src/protocol.o src/parse_data.o
CPPOBJS   = 
OBJS      = $(COBJS) $(CPPOBJS)
LIBS      = `pkg-config libusb-1.0 libudev --libs`
INCLUDES ?= `pkg-config libusb-1.0 --cflags`


polar: $(OBJS)
	$(CXX) $(CXXFLAGS) $(LDFLAGS) $^ $(LIBS) -o ./bin/polar

$(COBJS): %.o: %.c
	$(CC) $(CFLAGS) -c $(INCLUDES) $< -o $@

$(CPPOBJS): %.o: %.c
	$(CXX) $(CXXFLAGS) -c $(INCLUDES) $< -o $@

clean:
	rm -f $(OBJS) polar

.PHONY: clean
