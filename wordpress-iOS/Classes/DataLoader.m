//
//  OoIURLConnector.m
//  WordPress
//
//  Created by Shakir Ali on 20/07/2012.
//  Copyright (c) 2012 WordPress. All rights reserved.
//

#import "DataLoader.h"

@implementation DataLoader

@synthesize jsonData;
@synthesize dataFeedConnection;

-(void)initConnectionRequest{
    if (dataFeedConnection)
        [dataFeedConnection cancel];
    self.dataFeedConnection = nil;
    self.jsonData = nil;
}

-(void)submitURLRequest:(NSURLRequest*)urlRequest{
    NSURLConnection *urlConnection = [[NSURLConnection alloc] initWithRequest:urlRequest delegate:self];
    self.dataFeedConnection = urlConnection;
    [urlConnection release];
}

-(void)cancelRequest{
    [self initConnectionRequest];
}

-(void)dealloc{
    [dataFeedConnection release];
    [jsonData release];
    [super dealloc];
}

#pragma mark NSURLConnection functions.
- (void)connection:(NSURLConnection *)connection didReceiveResponse:(NSURLResponse *)response{
    self.jsonData = [NSMutableData data];
}

- (void)connection:(NSURLConnection *)connection didReceiveData:(NSData *)data{
    [self.jsonData appendData:data];
}

- (void)connectionDidFinishLoading:(NSURLConnection *)connection{
    self.dataFeedConnection = nil;
}

- (void)connection:(NSURLConnection *)connection didFailWithError:(NSError *)error{
    self.dataFeedConnection = nil;
}


@end
